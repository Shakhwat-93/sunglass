<?php

declare(strict_types=1);

use App\Support\LandingPageDefaults;

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__.'/../app/Support/LandingPageDefaults.php';

if (! function_exists('standalone_handle_request')) {
    function standalone_handle_request(): void
    {
        $page = LandingPageDefaults::data();
        $path = standalone_normalize_path($_SERVER['REQUEST_URI'] ?? '/');

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $path === '/order') {
            standalone_process_order($page);

            return;
        }

        http_response_code(200);

        if ($path === '/success') {
            standalone_render_success($page);

            return;
        }

        standalone_render_home($page);
    }

    function standalone_normalize_path(string $uri): string
    {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';

        if ($path === '/index.php') {
            return '/';
        }

        return rtrim($path, '/') ?: '/';
    }

    function standalone_env(string $key, ?string $default = null): ?string
    {
        static $values = null;

        if ($values === null) {
            $values = [];
            $envPath = dirname(__DIR__).'/.env';

            if (is_file($envPath)) {
                foreach (file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
                    if ($line === '' || str_starts_with(trim($line), '#') || ! str_contains($line, '=')) {
                        continue;
                    }

                    [$name, $value] = explode('=', $line, 2);
                    $name = trim($name);
                    $value = trim($value);

                    if ($value !== '' && (
                        (str_starts_with($value, '"') && str_ends_with($value, '"')) ||
                        (str_starts_with($value, "'") && str_ends_with($value, "'"))
                    )) {
                        $value = substr($value, 1, -1);
                    }

                    $values[$name] = $value;
                }
            }
        }

        return $values[$key] ?? $default;
    }

    function standalone_media(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return '/'.ltrim($path, '/');
    }

    function standalone_e(?string $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }

    function standalone_parse_price(string $value): float
    {
        return (float) preg_replace('/[^0-9.]/', '', $value);
    }

    function standalone_currency(float $value): string
    {
        return number_format($value, 0, '.', '').'৳';
    }

    function standalone_request(
        string $method,
        string $url,
        array $headers = [],
        ?array $payload = null
    ): array {
        $body = $payload ? json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : null;

        if (function_exists('curl_init')) {
            $ch = curl_init($url);

            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => $method,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_TIMEOUT => 20,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
            ]);

            if ($body !== null) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
            }

            $responseBody = (string) curl_exec($ch);
            $status = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            return [
                'status' => $status,
                'body' => $responseBody,
                'ok' => $error === '' && $status >= 200 && $status < 300,
                'error' => $error,
            ];
        }

        $context = stream_context_create([
            'http' => [
                'method' => $method,
                'header' => implode("\r\n", $headers),
                'content' => $body ?? '',
                'ignore_errors' => true,
                'timeout' => 20,
            ],
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ]);

        $responseBody = @file_get_contents($url, false, $context);
        $status = 0;

        foreach ($http_response_header ?? [] as $header) {
            if (preg_match('#HTTP/\S+\s+(\d{3})#', $header, $matches)) {
                $status = (int) $matches[1];
                break;
            }
        }

        return [
            'status' => $status,
            'body' => (string) $responseBody,
            'ok' => $status >= 200 && $status < 300,
            'error' => '',
        ];
    }

    function standalone_redirect(string $path): void
    {
        header('Location: '.$path);
        exit;
    }

    function standalone_flash_set(string $key, mixed $value): void
    {
        $_SESSION['_flash'][$key] = $value;
    }

    function standalone_flash_get(string $key, mixed $default = null): mixed
    {
        return $_SESSION['_flash'][$key] ?? $default;
    }

    function standalone_flash_pull(string $key, mixed $default = null): mixed
    {
        $value = $_SESSION['_flash'][$key] ?? $default;
        unset($_SESSION['_flash'][$key]);

        return $value;
    }

    function standalone_process_order(array $page): void
    {
        $customerFields = array_values($_POST['customer_fields'] ?? []);
        $deliveryIndex = isset($_POST['delivery_option']) ? (int) $_POST['delivery_option'] : -1;
        $deliveryOptions = array_values($page['checkout_delivery_options'] ?? []);

        standalone_flash_set('old', [
            'customer_fields' => $customerFields,
            'delivery_option' => $deliveryIndex,
        ]);

        if (
            count($customerFields) < 3 ||
            trim((string) ($customerFields[0] ?? '')) === '' ||
            trim((string) ($customerFields[1] ?? '')) === '' ||
            trim((string) ($customerFields[2] ?? '')) === ''
        ) {
            standalone_flash_set('error', 'Please fill in all required fields.');
            standalone_redirect('/#order');
        }

        $selectedDelivery = $deliveryOptions[$deliveryIndex] ?? ($deliveryOptions[0] ?? null);

        if (! $selectedDelivery) {
            standalone_flash_set('error', 'Please select a valid delivery option.');
            standalone_redirect('/#order');
        }

        $supabaseUrl = standalone_env('OMS_SUPABASE_URL');
        $supabaseKey = standalone_env('OMS_SUPABASE_KEY');

        if (! $supabaseUrl || ! $supabaseKey) {
            standalone_flash_set('error', 'Order system is not configured on this server yet.');
            standalone_redirect('/#order');
        }

        $phone = trim((string) $customerFields[2]);
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $whitelist = '01315183993';

        if ($phone !== $whitelist) {
            $query = http_build_query([
                'ip_address' => 'eq.'.$ip,
                'created_at' => 'gt.'.gmdate('c', time() - (3 * 60 * 60)),
                'select' => 'id',
                'limit' => 1,
            ]);

            $response = standalone_request(
                'GET',
                rtrim($supabaseUrl, '/').'/rest/v1/orders?'.$query,
                [
                    'apikey: '.$supabaseKey,
                    'Authorization: Bearer '.$supabaseKey,
                    'Accept: application/json',
                ],
            );

            $existingOrders = json_decode($response['body'], true);

            if ($response['ok'] && is_array($existingOrders) && $existingOrders !== []) {
                standalone_flash_set('error', 'Another recent order was detected from this device. Please try again later.');
                standalone_redirect('/#order');
            }
        }

        $quantity = isset($_POST['quantity']) ? (int) $_POST['quantity'] : 1;
        $quantity = max(1, min(4, $quantity));

        $subtotal = standalone_parse_price((string) ($page['checkout_subtotal'] ?? '0')) * $quantity;
        $delivery = standalone_parse_price((string) ($selectedDelivery['price'] ?? '0'));
        $total = $subtotal + $delivery;
        $orderId = 'ORD-'.strtoupper(bin2hex(random_bytes(4)));

        $selectedColors = $_POST['colors'] ?? [];
        $colorString = $selectedColors !== [] ? ' (Color: '.implode(', ', $selectedColors).')' : '';
        $productName = ($page['checkout_product_name'] ?? 'Product').($quantity > 1 ? " x{$quantity}" : "").$colorString;

        $payload = [
            'id' => $orderId,
            'customer_name' => trim((string) $customerFields[0]),
            'phone' => $phone,
            'address' => trim((string) $customerFields[1]),
            'ip_address' => $ip,
            'product_name' => $productName,
            'amount' => $total,
            'items' => $quantity,
            'status' => 'New',
            'source' => 'Website',
            'shipping_zone' => (string) (($selectedDelivery['label'] ?? 'Standard') . ' (৳' . standalone_parse_price((string) ($selectedDelivery['price'] ?? '0')) . ')'),
            'quantity' => $quantity,
            'payment_status' => 'Unpaid',
            'ordered_items' => [[
                'name' => $productName,
                'image' => (string) ($page['checkout_product_image'] ?? ''),
                'quantity' => $quantity,
                'price' => standalone_parse_price((string) ($page['checkout_subtotal'] ?? '0')),
            ]],
        ];

        $saveResponse = standalone_request(
            'POST',
            rtrim($supabaseUrl, '/').'/rest/v1/orders',
            [
                'apikey: '.$supabaseKey,
                'Authorization: Bearer '.$supabaseKey,
                'Content-Type: application/json',
                'Prefer: return=representation',
            ],
            $payload,
        );

        if (! $saveResponse['ok']) {
            standalone_flash_set('error', 'Order submission failed. Please try again or contact support.');
            standalone_redirect('/#order');
        }

        unset($_SESSION['_flash']['old']);
        $_SESSION['last_order'] = [
            'order_id' => $orderId,
            'customer_name' => trim((string) $customerFields[0]),
            'customer_address' => trim((string) $customerFields[1]),
            'customer_phone' => $phone,
            'order_total' => $total,
        ];

        standalone_redirect('/success');
    }

    function standalone_render_home(array $page): void
    {
        $brandSuffix = str_starts_with((string) $page['brand_name'], (string) $page['brand_accent'])
            ? substr((string) $page['brand_name'], strlen((string) $page['brand_accent']))
            : (string) $page['brand_name'];

        $old = standalone_flash_pull('old', [
            'customer_fields' => [],
            'delivery_option' => null,
        ]);
        $error = standalone_flash_pull('error');
        $selectedDelivery = $old['delivery_option'];

        if ($selectedDelivery === null) {
            foreach (($page['checkout_delivery_options'] ?? []) as $index => $option) {
                if (! empty($option['checked'])) {
                    $selectedDelivery = $index;
                    break;
                }
            }
        }
        ?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= standalone_e($page['meta_title'] ?? 'Product') ?></title>
    <meta name="description" content="<?= standalone_e($page['meta_description'] ?? '') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Noto+Sans+Bengali:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="preload" as="image" href="<?= standalone_e(standalone_media($page['hero_image'] ?? '') ?? '') ?>">
    <link rel="stylesheet" href="/landing.css?v=1.3">
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','GTM-PMHNV9HJ');</script>
    <script>
        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push({
            event: 'view_item',
            ecommerce: {
                currency: 'BDT',
                value: <?= json_encode(standalone_parse_price((string) ($page['checkout_subtotal'] ?? '0'))) ?>,
                items: [{
                    item_id: 'sunglass-01',
                    item_name: <?= json_encode($page['checkout_product_name'] ?? 'Product') ?>,
                    price: <?= json_encode(standalone_parse_price((string) ($page['checkout_subtotal'] ?? '0'))) ?>,
                    quantity: 1
                }]
            }
        });
    </script>
</head>
<body>
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-PMHNV9HJ" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>

    <header class="site-header container">
        <a class="brand" href="#hero" aria-label="<?= standalone_e(($page['brand_name'] ?? 'Brand').' home') ?>">
            <strong><span><?= standalone_e($page['brand_accent'] ?? '') ?></span><?= standalone_e($brandSuffix) ?></strong>
        </a>
    </header>

    <main>
        <section class="hero section container" id="hero">
            <div class="hero__content">
                <div class="hero__headline">
                    <h1><?= $page['hero_headline'] ?? '' ?></h1>
                    <div class="hero__actions" style="margin-top: 32px;">
                        <a href="#order" class="btn btn--primary">
                            <span>অর্ডার করতে এখানে ক্লিক করুন</span>
                            <svg style="width: 20px; height: 20px; margin-left:8px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>
            </div>
            <div class="hero__media">
                <div class="hero-card">
                    <img src="<?= standalone_e(standalone_media($page['hero_image'] ?? '') ?? '') ?>" alt="<?= standalone_e(($page['brand_name'] ?? 'Brand').' hero product image') ?>">
                </div>
            </div>
        </section>

        <section class="section" id="product-video" style="padding-top: 0;">
            <div class="container">
                <div class="section-heading section-heading--center">
                    <span class="eyebrow">Product Showcase</span>
                    <h2>চশমাটি বাস্তবে দেখুন (ভিডিও)</h2>
                    <p>প্রিমিয়াম কোয়ালিটি এবং নিখুঁত ফিনিশিং এর লাইভ ডেমো ভিডিওটি উপভোগ করুন।</p>
                </div>

                <div class="video-container">
                    <div class="video-wrapper">
                        <iframe 
                            src="https://www.youtube.com/embed/IyGzRkbYZ-Y" 
                            title="Product Video" 
                            frameborder="0" 
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                            allowfullscreen>
                        </iframe>
                    </div>
                </div>

                <div class="hero__actions" style="margin-top: 40px; display: flex; justify-content: center;">
                    <a href="#order" class="btn btn--primary">
                        <span>অর্ডার করতে এখানে ক্লিক করুন</span>
                        <svg style="width: 20px; height: 20px; margin-left:8px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
        </section>

        <section class="section section--soft" id="how-it-works">
            <div class="container">
                <div class="section-heading section-heading--center section-heading--showcase">
                    <span class="eyebrow"><?= standalone_e($page['how_it_works_badge'] ?? '') ?></span>
                    <h2><?= $page['how_it_works_title'] ?? '' ?></h2>
                    <?php if (! empty($page['how_it_works_description'])): ?>
                        <p><?= standalone_e($page['how_it_works_description']) ?></p>
                    <?php endif; ?>
                </div>
                <div class="dial-grid">
                    <?php foreach (($page['how_it_works_steps'] ?? []) as $step): ?>
                        <article class="dial-card">
                            <div class="dial-card__media">
                                <img src="<?= standalone_e(standalone_media($step['image'] ?? '') ?? '') ?>" alt="<?= standalone_e($step['title'] ?? 'How it works step') ?>">
                            </div>
                            <span class="dial-card__step"><?= standalone_e($step['step'] ?? '') ?></span>
                            <h3><?= standalone_e($step['title'] ?? '') ?></h3>
                            <p><?= standalone_e($step['description'] ?? '') ?></p>
                        </article>
                    <?php endforeach; ?>
                </div>
                <div class="hero__actions" style="margin-top: 48px; display: flex; justify-content: center;">
                    <a href="#order" class="btn btn--primary">
                        <span>অর্ডার করতে এখানে ক্লিক করুন</span>
                        <svg style="width: 20px; height: 20px; margin-left:8px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
        </section>

        <section class="section" id="benefits">
            <div class="container">
                <div class="section-heading section-heading--center">
                    <span class="eyebrow"><?= standalone_e($page['benefits_badge'] ?? '') ?></span>
                    <h2><?= $page['benefits_title'] ?? '' ?></h2>
                    <?php if (! empty($page['benefits_description'])): ?>
                        <p><?= standalone_e($page['benefits_description']) ?></p>
                    <?php endif; ?>
                </div>
                <div class="benefit-grid">
                    <?php foreach (($page['benefits_items'] ?? []) as $benefit): ?>
                        <article class="benefit-card<?= ! empty($benefit['featured']) ? ' benefit-card--wide' : '' ?>">
                            <div class="icon-box">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M8 12h8"/><path d="M12 8v8"/></svg>
                            </div>
                            <h3><?= standalone_e($benefit['title'] ?? '') ?></h3>
                            <p><?= standalone_e($benefit['description'] ?? '') ?></p>
                        </article>
                    <?php endforeach; ?>
                </div>
                <div class="hero__actions" style="margin-top: 48px; display: flex; justify-content: center;">
                    <a href="#order" class="btn btn--primary">
                        <span>অর্ডার করতে এখানে ক্লিক করুন</span>
                        <svg style="width: 20px; height: 20px; margin-left:8px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
        </section>

        <section class="section section--soft" id="features">
            <div class="container">
                <div class="section-heading section-heading--split">
                    <span class="eyebrow"><?= standalone_e($page['features_badge'] ?? '') ?></span>
                    <h2><?= $page['features_title'] ?? '' ?></h2>
                    <?php if (! empty($page['features_image'])): ?>
                        <div class="section-heading__preview">
                            <img src="<?= standalone_e(standalone_media($page['features_image']) ?? '') ?>" alt="<?= standalone_e(strip_tags((string) ($page['features_title'] ?? ''))) ?>">
                        </div>
                    <?php endif; ?>
                </div>
                <div class="spec-layout">
                    <div class="spec-list">
                        <?php foreach (($page['feature_items'] ?? []) as $feature): ?>
                            <article class="spec-item">
                                <h3><?= standalone_e($feature['title'] ?? '') ?></h3>
                                <p><?= standalone_e($feature['description'] ?? '') ?></p>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </section>

        <section class="section" id="gallery">
            <div class="container">
                <div class="section-heading section-heading--center">
                    <span class="eyebrow"><?= standalone_e($page['gallery_badge'] ?? '') ?></span>
                    <h2><?= $page['gallery_title'] ?? '' ?></h2>
                    <?php if (! empty($page['gallery_description'])): ?>
                        <p><?= standalone_e($page['gallery_description']) ?></p>
                    <?php endif; ?>
                </div>
                <div class="gallery-grid">
                    <?php foreach (($page['gallery_items'] ?? []) as $item): ?>
                        <figure class="gallery-card<?= ! empty($item['featured']) ? ' gallery-card--large' : '' ?>">
                            <img src="<?= standalone_e(standalone_media($item['image'] ?? '') ?? '') ?>" alt="<?= standalone_e($item['alt'] ?? 'Gallery image') ?>">
                        </figure>
                    <?php endforeach; ?>
                </div>
                <div class="hero__actions" style="margin-top: 40px; display: flex; justify-content: center;">
                    <a href="#order" class="btn btn--primary">
                        <span>অর্ডার করতে এখানে ক্লিক করুন</span>
                        <svg style="width: 20px; height: 20px; margin-left:8px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
        </section>

        <section class="section" id="order">
            <div class="container">
                <div class="order-card">
                    <?php if ($error): ?>
                        <div class="order-error"><?= standalone_e((string) $error) ?></div>
                    <?php endif; ?>
                    <div class="checkout-grid">
                        <div class="checkout-panel">
                            <h2><?= standalone_e($page['checkout_title'] ?? 'Billing & Shipping') ?></h2>
                            <form class="order-form checkout-form" id="checkout-form" method="POST" action="/order">
                                <?php foreach (($page['checkout_fields'] ?? []) as $index => $field): ?>
                                    <label class="checkout-field">
                                        <span class="checkout-field__label">
                                            <?= standalone_e($field['label'] ?? '') ?>
                                            <?php if (! empty($field['required'])): ?>
                                                <em>*</em>
                                            <?php endif; ?>
                                        </span>
                                        <input
                                            type="text"
                                            name="customer_fields[]"
                                            placeholder="<?= standalone_e($field['placeholder'] ?? '') ?>"
                                            value="<?= standalone_e($old['customer_fields'][$index] ?? '') ?>"
                                            <?= ! empty($field['required']) ? 'required' : '' ?>
                                        >
                                    </label>
                                <?php endforeach; ?>

                                <div class="quantity-selection">
                                    <span class="checkout-field__label">পরিমাণ (Quantity) - সর্বোচ্চ ৪টি <em>*</em></span>
                                    <div class="qty-controls">
                                        <button type="button" class="qty-btn qty-btn--minus" aria-label="Decrease quantity">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                        </button>
                                        <input type="number" name="quantity" id="order-qty" value="1" min="1" max="4" readonly>
                                        <button type="button" class="qty-btn qty-btn--plus" aria-label="Increase quantity">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                        </button>
                                    </div>
                                </div>

                                <div class="color-selection">
                                    <span class="checkout-field__label">পছন্দের কালার সিলেক্ট করুন <em>*</em></span>
                                    <div class="color-options">
                                        <label class="color-option">
                                            <input type="checkbox" name="colors[]" value="Black" checked>
                                            <span class="color-swatch color-swatch--black"></span>
                                            <span class="color-name">Black</span>
                                        </label>
                                        <label class="color-option">
                                            <input type="checkbox" name="colors[]" value="Golden">
                                            <span class="color-swatch color-swatch--golden"></span>
                                            <span class="color-name">Golden</span>
                                        </label>
                                        <label class="color-option">
                                            <input type="checkbox" name="colors[]" value="Silver">
                                            <span class="color-swatch color-swatch--silver"></span>
                                            <span class="color-name">Silver</span>
                                        </label>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <aside class="checkout-summary">
                            <h2><?= standalone_e($page['checkout_summary_title'] ?? 'Your order') ?></h2>
                            <div class="summary-table">
                                <div class="summary-table__head">
                                    <span>Product</span>
                                    <span>Subtotal</span>
                                </div>
                                <div class="summary-product">
                                    <div class="summary-product__item">
                                        <div class="summary-product__thumb">
                                            <img src="<?= standalone_e(standalone_media($page['checkout_product_image'] ?? '') ?? '') ?>" alt="<?= standalone_e($page['checkout_product_name'] ?? 'Product') ?>">
                                        </div>
                                        <div class="summary-product__meta">
                                            <strong><?= standalone_e($page['checkout_product_name'] ?? 'Product') ?></strong>
                                            <span id="summary-qty">&times; 1</span>
                                        </div>
                                    </div>
                                    <div class="summary-price-wrapper">
                                        <?php if (! empty($page['checkout_original_price'])): ?>
                                            <del class="summary-price-old"><?= standalone_e($page['checkout_original_price']) ?></del>
                                        <?php endif; ?>
                                        <strong class="summary-price" id="summary-price"><?= standalone_e($page['checkout_subtotal'] ?? '') ?></strong>
                                    </div>
                                </div>
                                <div class="summary-row">
                                    <span>Subtotal</span>
                                    <strong id="subtotal-display" data-value="<?= standalone_e($page['checkout_subtotal'] ?? '0') ?>"><?= standalone_e($page['checkout_subtotal'] ?? '') ?></strong>
                                </div>
                                <div class="delivery-options">
                                    <?php foreach (($page['checkout_delivery_options'] ?? []) as $index => $option): ?>
                                        <label class="delivery-option">
                                            <span><?= standalone_e($option['label'] ?? '') ?>: <strong><?= standalone_e($option['price'] ?? '') ?></strong></span>
                                            <input type="radio" name="delivery_option" value="<?= $index ?>" data-price="<?= standalone_e($option['price'] ?? '0') ?>" form="checkout-form" <?= (string) $selectedDelivery === (string) $index ? 'checked' : '' ?>>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                                <div class="summary-total">
                                    <span>Total</span>
                                    <strong id="total-display"><?= standalone_e($page['checkout_total'] ?? '') ?></strong>
                                </div>
                            </div>
                            <button type="submit" class="checkout-submit" form="checkout-form">
                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M17 9h-1V7a4 4 0 0 0-8 0v2H7a2 2 0 0 0-2 2v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-7a2 2 0 0 0-2-2Zm-7-2a2 2 0 1 1 4 0v2h-4V7Zm7 11H7v-7h10v7Z" /></svg>
                                <span><?= standalone_e($page['checkout_button_text'] ?? 'Confirm Order') ?></span>
                            </button>
                            <?php if (! empty($page['support_phone'])): ?>
                                <p class="checkout-help">Need help? Call <a href="tel:<?= standalone_e(preg_replace('/\s+/', '', (string) $page['support_phone'])) ?>"><?= standalone_e($page['support_phone']) ?></a></p>
                            <?php endif; ?>
                        </aside>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="site-footer container">
        <div><strong><?= standalone_e($page['footer_brand'] ?? '') ?></strong></div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const totalDisplay = document.getElementById('total-display');
            const subtotalDisplay = document.getElementById('subtotal-display');
            const deliveryInputs = document.querySelectorAll('input[name="delivery_option"]');

            // Quantity logic
            const qtyInput = document.getElementById('order-qty');
            const minusBtn = document.querySelector('.qty-btn--minus');
            const plusBtn = document.querySelector('.qty-btn--plus');

            if (qtyInput && minusBtn && plusBtn) {
                minusBtn.addEventListener('click', function() {
                    const current = parseInt(qtyInput.value) || 1;
                    if (current > 1) {
                        qtyInput.value = current - 1;
                        updateTotal();
                    }
                });

                plusBtn.addEventListener('click', function() {
                    const current = parseInt(qtyInput.value) || 1;
                    if (current < 4) {
                        qtyInput.value = current + 1;
                        updateTotal();
                    }
                });
            }

            function parsePrice(str) {
                return parseFloat(String(str || '').replace(/[^0-9.]/g, '')) || 0;
            }

            function formatPrice(num) {
                return num.toFixed(0) + '৳';
            }

            function updateTotal() {
                const baseSubtotal = parsePrice(subtotalDisplay.getAttribute('data-value'));
                const qty = parseInt(qtyInput.value) || 1;
                const subtotal = baseSubtotal * qty;
                
                let delivery = 0;
                deliveryInputs.forEach(function (input) {
                    if (input.checked) {
                        delivery = parsePrice(input.getAttribute('data-price'));
                    }
                });
                
                const summaryQty = document.getElementById('summary-qty');
                const summaryPrice = document.getElementById('summary-price');

                if (subtotalDisplay) subtotalDisplay.textContent = formatPrice(subtotal);
                if (totalDisplay) totalDisplay.textContent = formatPrice(subtotal + delivery);
                if (summaryQty) summaryQty.textContent = '× ' + qty;
                if (summaryPrice) summaryPrice.textContent = formatPrice(subtotal);
            }

            deliveryInputs.forEach(function (input) {
                input.addEventListener('change', updateTotal);
            });

            updateTotal();
        });
    </script>
</body>
</html>
<?php
    }

    function standalone_render_success(array $page): void
    {
        $order = $_SESSION['last_order'] ?? [];
        $brandSuffix = str_starts_with((string) $page['brand_name'], (string) $page['brand_accent'])
            ? substr((string) $page['brand_name'], strlen((string) $page['brand_accent']))
            : (string) $page['brand_name'];
        ?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Order Successful | <?= standalone_e($page['brand_name'] ?? 'Brand') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Noto+Sans+Bengali:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/landing.css?v=1.3">
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','GTM-PMHNV9HJ');</script>
    <script>
        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push({
            event: 'purchase',
            customer_name: <?= json_encode($order['customer_name'] ?? '') ?>,
            customer_address: <?= json_encode($order['customer_address'] ?? '') ?>,
            customer_phone: <?= json_encode($order['customer_phone'] ?? '') ?>,
            ecommerce: {
                transaction_id: <?= json_encode($order['order_id'] ?? '') ?>,
                value: <?= json_encode((float) ($order['order_total'] ?? 0)) ?>,
                currency: 'BDT',
                items: [{
                    item_id: 'sunglass-01',
                    item_name: <?= json_encode($page['checkout_product_name'] ?? 'Product') ?>,
                    price: <?= json_encode((float) ($order['order_total'] ?? 0)) ?>,
                    quantity: 1
                }]
            }
        });
    </script>
    <style>
        :root { --success-color: #10b981; }
        body { background-color: #ffffff; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; font-family: 'Manrope', 'Noto Sans Bengali', sans-serif; }
        .success-container { text-align: center; padding: 3rem; max-width: 500px; width: 90%; background: #ffffff; border-radius: 24px; box-shadow: 0 20px 40px rgba(0, 0, 0, 0.05); animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1); }
        @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .success-icon { width: 80px; height: 80px; background: rgba(16, 185, 129, 0.1); color: var(--success-color); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 2rem; animation: scaleIn 0.5s cubic-bezier(0.34, 1.56, 0.64, 1); }
        @keyframes scaleIn { from { transform: scale(0); } to { transform: scale(1); } }
        .success-icon svg { width: 40px; height: 40px; }
        h1 { font-size: 2rem; font-weight: 800; color: #111827; margin-bottom: 1rem; line-height: 1.2; }
        p { font-size: 1.1rem; color: #6b7280; line-height: 1.6; margin-bottom: 2.5rem; }
        .btn-home { display: inline-flex; align-items: center; justify-content: center; background: #111827; color: #ffffff; padding: 1rem 2rem; border-radius: 12px; font-weight: 600; text-decoration: none; transition: all 0.3s ease; gap: 0.5rem; }
        .btn-home:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); background: #000; }
        .btn-home svg { width: 20px; height: 20px; }
        .brand-logo { margin-bottom: 3rem; display: block; text-decoration: none; color: #111827; font-size: 1.5rem; font-weight: 800; }
        .brand-logo span { color: #ff6a35; }
    </style>
</head>
<body>
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-PMHNV9HJ" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <div class="success-container">
        <a href="/" class="brand-logo"><span><?= standalone_e($page['brand_accent'] ?? '') ?></span><?= standalone_e($brandSuffix) ?></a>
        <div class="success-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
        </div>
        <h1>Order successful</h1>
        <p>Your order has been received. One of our team members will contact you shortly.</p>
        <a href="/" class="btn-home">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
            <span>Back to home</span>
        </a>
    </div>
</body>
</html>
<?php
    }
}

standalone_handle_request();
