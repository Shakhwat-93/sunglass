<?php

namespace App\Http\Controllers;

use App\Models\LandingPage;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $page = LandingPage::home();
        $deliveryOptions = collect($page->checkout_delivery_options ?? [])->values();

        $validated = $request->validate([
            'customer_fields' => ['required', 'array', 'min:3'],
            'customer_fields.0' => ['required', 'string', 'max:255'],
            'customer_fields.1' => ['required', 'string', 'max:1000'],
            'customer_fields.2' => ['required', 'string', 'max:32'],
            'delivery_option' => ['required', 'integer', 'min:0'],
        ]);

        $ip = $request->ip();
        $phone = trim($validated['customer_fields'][2]);
        $whitelist = '01315183993';

        // Fake Order Protection Logic
        if ($phone !== $whitelist) {
            $threeHoursAgo = now()->subHours(3)->toIso8601String();
            
            $checkResponse = \Illuminate\Support\Facades\Http::withoutVerifying()
                ->withHeaders([
                    'apikey' => env('OMS_SUPABASE_KEY'),
                    'Authorization' => 'Bearer ' . env('OMS_SUPABASE_KEY'),
                ])->get(env('OMS_SUPABASE_URL') . '/rest/v1/orders', [
                    'ip_address' => 'eq.' . $ip,
                    'created_at' => 'gt.' . $threeHoursAgo,
                    'select' => 'id',
                    'limit' => 1
                ]);

            if ($checkResponse->successful() && !empty($checkResponse->json())) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors(['rate_limit' => 'দুঃখিত, ফেইক অর্ডার রোধে আপনার ডিভাইস থেকে এই মুহূর্তে আর অর্ডার করা যাবে না।']);
            }
        }


        $deliveryIndex = (int) $validated['delivery_option'];
        $selectedDelivery = $deliveryOptions->get($deliveryIndex) ?? $deliveryOptions->first();

        abort_if(! $selectedDelivery, 422, 'Delivery option is invalid.');

        $quantity = (int) $request->input('quantity', 1);
        $quantity = max(1, min(4, $quantity)); // Clamp between 1 and 4

        $totalAmount = $this->resolveTotal($page, $selectedDelivery, $quantity);
        
        $orderId = 'ORD-' . strtoupper(\Illuminate\Support\Str::random(8));

        $selectedColors = $request->input('colors', []);

        $colorString = !empty($selectedColors) ? ' (Color: ' . implode(', ', $selectedColors) . ')' : '';
        $productName = $page->checkout_product_name . ($quantity > 1 ? " x{$quantity}" : "") . $colorString;

        // Exclusively save to External OMS via Supabase API
        $response = \Illuminate\Support\Facades\Http::withoutVerifying()
            ->withHeaders([
                'apikey' => env('OMS_SUPABASE_KEY'),
                'Authorization' => 'Bearer ' . env('OMS_SUPABASE_KEY'),
                'Content-Type' => 'application/json',
                'Prefer' => 'return=representation'
            ])->post(env('OMS_SUPABASE_URL') . '/rest/v1/orders', [
                'id' => $orderId, // Required field in OMS schema
                'customer_name' => trim($validated['customer_fields'][0]),
                'phone' => trim($validated['customer_fields'][2]),
                'address' => trim($validated['customer_fields'][1]),
                'ip_address' => $ip, // Store IP for future checks
                'product_name' => $productName,
                'amount' => (float) str_replace(',', '', preg_replace('/[^0-9.]/', '', $totalAmount)),
                'items' => $quantity,
                'status' => 'New',
                'source' => 'Website',
                'shipping_zone' => (string) (($selectedDelivery['label'] ?? 'Outside dhaka') . ' (৳' . (float) str_replace(',', '', preg_replace('/[^0-9.]/', '', $selectedDelivery['price'] ?? '0')) . ')'),
                'quantity' => $quantity,
                'payment_status' => 'Unpaid',
                'ordered_items' => [[
                    'name' => $productName,
                    'image' => (string) ($page->checkout_product_image ?? ''),
                    'quantity' => $quantity,
                    'price' => (float) str_replace(',', '', preg_replace('/[^0-9.]/', '', $page->checkout_subtotal)),
                ]]
            ]);

        if ($response->failed()) {
            \Illuminate\Support\Facades\Log::error('OMS Order Sync Failed', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            
            // Show exact error if failed, as requested for debugging
            abort(500, "OMS Sync Error ({$response->status()}): " . $response->body());
        }

        return redirect()
            ->route('order.success')
            ->with('order_id', $orderId)
            ->with('customer_name', trim($validated['customer_fields'][0]))
            ->with('customer_address', trim($validated['customer_fields'][1]))
            ->with('customer_phone', trim($validated['customer_fields'][2]))
            ->with('order_total', (float) str_replace(',', '', preg_replace('/[^0-9.]/', '', $totalAmount)));
    }

    public function success(): \Illuminate\View\View
    {
        return view('success', [
            'page' => LandingPage::home(),
        ]);
    }


    private function resolveTotal(LandingPage $page, array $selectedDelivery, int $quantity = 1): string
    {
        $parsePrice = fn($str) => (float) str_replace(',', '', preg_replace('/[^0-9.]/', '', $str));

        $subtotal = $parsePrice($page->checkout_subtotal) * $quantity;
        $delivery = $parsePrice($selectedDelivery['price'] ?? '0');

        return ($subtotal + $delivery) . '৳';
    }
}
