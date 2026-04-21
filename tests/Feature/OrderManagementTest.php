<?php

namespace Tests\Feature;

use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_place_order_from_landing_page(): void
    {
        $response = $this->post(route('order.store'), [
            'customer_fields' => [
                'Hasan Mahmud',
                'House 12, Road 4, Dhanmondi, Dhaka',
                '01700000000',
            ],
            'delivery_option' => 1,
        ]);

        $response->assertRedirect(route('landing'));
        $response->assertSessionHas('order_status');

        $this->assertDatabaseHas('orders', [
            'customer_name' => 'Hasan Mahmud',
            'phone' => '01700000000',
            'status' => 'new',
        ]);
    }

    public function test_admin_can_update_order_status(): void
    {
        $order = Order::query()->create([
            'customer_name' => 'Hasan Mahmud',
            'shipping_address' => 'Dhaka',
            'phone' => '01700000000',
            'product_name' => 'Canvas Airflow Moshari - Olive',
            'subtotal' => '998৳',
            'delivery_label' => 'ঢাকার ভিতরে ডেলিভারি চার্জ',
            'delivery_price' => '60.00৳',
            'total' => '1058৳',
            'status' => 'new',
            'source' => 'landing-page',
            'items' => [['name' => 'Canvas Airflow Moshari - Olive', 'quantity' => 1]],
            'customer_fields' => [['label' => 'Name', 'value' => 'Hasan Mahmud']],
        ]);

        $response = $this->withSession([
            'admin_authenticated' => true,
            'admin_email' => 'admin@example.com',
        ])->patch(route('admin.orders.update', $order), [
            'status' => 'confirmed',
            'admin_note' => 'Call confirmed',
        ]);

        $response->assertRedirect();

        $order->refresh();

        $this->assertSame('confirmed', $order->status);
        $this->assertSame('Call confirmed', $order->admin_note);
    }

    public function test_admin_can_view_order_details_on_orders_page(): void
    {
        Order::query()->create([
            'customer_name' => 'Shakil Ahmed',
            'shipping_address' => 'Mirpur, Dhaka',
            'phone' => '01800000000',
            'product_name' => 'Canvas Airflow Moshari - Olive',
            'subtotal' => '998৳',
            'delivery_label' => 'ঢাকার ভিতরে ডেলিভারি চার্জ',
            'delivery_price' => '60.00৳',
            'total' => '1058৳',
            'status' => 'processing',
            'source' => 'landing-page',
            'items' => [['name' => 'Canvas Airflow Moshari - Olive', 'quantity' => 1]],
            'customer_fields' => [
                ['label' => 'Name', 'value' => 'Shakil Ahmed'],
                ['label' => 'Address', 'value' => 'Mirpur, Dhaka'],
                ['label' => 'Phone', 'value' => '01800000000'],
            ],
        ]);

        $response = $this->withSession([
            'admin_authenticated' => true,
            'admin_email' => 'admin@example.com',
        ])->get(route('admin.orders.index'));

        $response->assertOk();
        $response->assertSeeText('Incoming Orders');
        $response->assertSeeText('View Details');
        $response->assertSeeText('Shakil Ahmed');
        $response->assertSeeText('Mirpur, Dhaka');
        $response->assertSeeText('Processing');
    }
}
