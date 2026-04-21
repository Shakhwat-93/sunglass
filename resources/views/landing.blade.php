<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $page->meta_title }}</title>
    <meta name="description" content="{{ $page->meta_description }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Noto+Sans+Bengali:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="preload" as="image" href="{{ str_starts_with($page->hero_image, 'http') ? $page->hero_image : asset(ltrim($page->hero_image, '/')) }}">
    <link rel="stylesheet" href="{{ asset('landing.css') }}?v=1.4">
    
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-PMHNV9HJ');</script>
    <!-- End Google Tag Manager -->

    <script>
        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push({
            'event': 'view_item',
            'ecommerce': {
                'currency': 'BDT',
                'value': 1199,
                'items': [{
                    'item_id': 'sunglass-01',
                    'item_name': 'High Quality Adjustable Dimming Polarized Sunglass',
                    'price': 1199,
                    'quantity': 1
                }]
            }
        });
    </script>
</head>
<body>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-PMHNV9HJ"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
@php
    $media = static function (?string $path): ?string {
        if (blank($path)) {
            return null;
        }

        return str_starts_with($path, 'http://') || str_starts_with($path, 'https://')
            ? $path
            : asset(ltrim($path, '/'));
    };

    $brandSuffix = \Illuminate\Support\Str::startsWith($page->brand_name, $page->brand_accent)
        ? \Illuminate\Support\Str::after($page->brand_name, $page->brand_accent)
        : $page->brand_name;
@endphp

    <header class="site-header container">
        <a class="brand" href="#hero" aria-label="{{ $page->brand_name }} home">
            <strong><span>{{ $page->brand_accent }}</span>{{ $brandSuffix }}</strong>
        </a>
    </header>

    <main>
        <section class="hero section container" id="hero">
            <div class="hero__content">
                <div class="hero__headline">
                    <h1>{!! $page->hero_headline !!}</h1>
                    <div class="hero__actions" style="margin-top: 32px;">
                        <a href="#order-form" class="btn btn--primary">
                            <span>অর্ডার করতে এখানে ক্লিক করুন</span>
                            <svg style="width: 20px; height: 20px; margin-left:8px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>
            </div>

            <div class="hero__media">
                <div class="hero-card">
                    <img src="{{ $media($page->hero_image) }}" alt="{{ $page->brand_name }} hero product image">
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

                <div class="hero__actions" style="margin-top: 40px; display: flex; justify-content: center;">
                    <a href="#order-form" class="btn btn--primary">
                        <span>অর্ডার করতে এখানে ক্লিক করুন</span>
                        <svg style="width: 20px; height: 20px; margin-left:8px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
        </section>

        <section class="section section--soft" id="how-it-works">
            <div class="container">
                <div class="section-heading section-heading--center section-heading--showcase">
                    <span class="eyebrow">{{ $page->how_it_works_badge }}</span>
                    <h2>{!! $page->how_it_works_title !!}</h2>
                    @if ($page->how_it_works_description)
                        <p>{{ $page->how_it_works_description }}</p>
                    @endif
                </div>

                <div class="dial-grid">
                    @foreach ($page->how_it_works_steps as $step)
                        <article class="dial-card">
                            <div class="dial-card__media">
                                <img src="{{ $media($step['image'] ?? null) }}" alt="{{ $step['title'] ?? 'How it works step' }}">
                            </div>
                            <span class="dial-card__step">{{ $step['step'] ?? '' }}</span>
                            <h3>{{ $step['title'] ?? '' }}</h3>
                            <p>{{ $step['description'] ?? '' }}</p>
                        </article>
                    @endforeach
                </div>
                <div class="hero__actions" style="margin-top: 48px; display: flex; justify-content: center;">
                    <a href="#order-form" class="btn btn--primary">
                        <span>অর্ডার করতে এখানে ক্লিক করুন</span>
                        <svg style="width: 20px; height: 20px; margin-left:8px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
        </section>

        <section class="section" id="benefits">
            <div class="container">
                <div class="section-heading section-heading--center">
                    <span class="eyebrow">{{ $page->benefits_badge }}</span>
                    <h2>{!! $page->benefits_title !!}</h2>
                    @if ($page->benefits_description)
                        <p>{{ $page->benefits_description }}</p>
                    @endif
                </div>

                <div class="benefit-grid">
                    @foreach ($page->benefits_items as $index => $benefit)
                        @php
                            $title = $benefit['title'] ?? '';
                            $icon = match(true) {
                                str_contains($title, 'বাতাস') || str_contains($title, 'বাযু') => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12.8 19.6a2.1 2.1 0 1 0 1.8-3.3H2"/><path d="M10.1 9.4a2.1 2.1 0 1 1 1.8 3.3H2"/><path d="M19.5 12.5a2.1 2.1 0 1 0-1.8 3.2h20"/><path d="M16.2 5.5a2.1 2.1 0 1 1-1.8 3.3H2"/></svg>',
                                str_contains($title, 'জিপার') => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10 2v3m0 0h3a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-4a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2z"/><path d="M10 11v4"/><path d="M12 11v4"/></svg>',
                                str_contains($title, 'ফিনিশিং') || str_contains($title, 'কোয়ালিটি') => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>',
                                str_contains($title, 'ধোয়া') || str_contains($title, 'পরিষ্কার') => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M2 11l2 11h16l2-11H2z"/><path d="M2.2 11c.6-1.5 2.1-2.5 3.8-2.5h12c1.7 0 3.2 1 3.8 2.5"/><circle cx="12" cy="16.5" r="2.5"/></svg>',
                                default => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z"/><path d="M12 8V12L15 15"/></svg>'
                            };
                        @endphp
                        <article class="benefit-card @if(!empty($benefit['featured'])) benefit-card--wide @endif">
                            <div class="icon-box">
                                {!! $icon !!}
                            </div>
                            <h3>{{ $title }}</h3>
                            <p>{{ $benefit['description'] ?? '' }}</p>
                        </article>
                    @endforeach
                </div>
                <div class="hero__actions" style="margin-top: 48px; display: flex; justify-content: center;">
                    <a href="#order-form" class="btn btn--primary">
                        <span>অর্ডার করতে এখানে ক্লিক করুন</span>
                        <svg style="width: 20px; height: 20px; margin-left:8px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
        </section>

        <section class="section section--soft" id="features">
            <div class="container">
                <div class="section-heading section-heading--split">
                    <span class="eyebrow">{{ $page->features_badge }}</span>
                    <h2>{!! $page->features_title !!}</h2>
                    @if ($page->features_image)
                        <div class="section-heading__preview">
                            <img src="{{ $media($page->features_image) }}" alt="{{ $page->features_title }}">
                        </div>
                    @endif
                </div>

                <div class="spec-layout">
                    <div class="spec-list">
                        @foreach ($page->feature_items as $feature)
                            <article class="spec-item">
                                <h3>{{ $feature['title'] ?? '' }}</h3>
                                <p>{{ $feature['description'] ?? '' }}</p>
                            </article>
                        @endforeach
                    </div>


                </div>
            </div>
        </section>

        <section class="section" id="gallery">
            <div class="container">
                <div class="section-heading section-heading--center">
                    <span class="eyebrow">{{ $page->gallery_badge }}</span>
                    <h2>{!! $page->gallery_title !!}</h2>
                    @if ($page->gallery_description)
                        <p>{{ $page->gallery_description }}</p>
                    @endif
                </div>

                <div class="gallery-grid">
                    @foreach ($page->gallery_items as $item)
                        <figure class="gallery-card @if(!empty($item['featured'])) gallery-card--large @endif">
                            <img src="{{ $media($item['image'] ?? null) }}" alt="{{ $item['alt'] ?? 'Gallery image' }}">
                        </figure>
                    @endforeach
                </div>

                <div class="hero__actions" style="margin-top: 40px; display: flex; justify-content: center;">
                    <a href="#order-form" class="btn btn--primary">
                        <span>অর্ডার করতে এখানে ক্লিক করুন</span>
                        <svg style="width: 20px; height: 20px; margin-left:8px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
        </section>



        <section class="section" id="order">
            <div class="container">
                <div class="order-card">
                    @if (session('order_status'))
                        <div class="order-success">{{ session('order_status') }}</div>
                    @endif
                    @if ($errors->any() && !$errors->has('rate_limit'))
                        <div class="order-error">অর্ডার সাবমিট করতে সব required field ঠিকভাবে পূরণ করুন।</div>
                    @endif

                    <div class="checkout-grid">
                        <div class="checkout-panel">
                            <h2>{{ $page->checkout_title }}</h2>

                            <form class="order-form checkout-form" id="checkout-form" method="POST" action="{{ route('order.store') }}">
                                @csrf
                                @foreach ($page->checkout_fields as $index => $field)
                                    <label class="checkout-field">
                                        <span class="checkout-field__label">
                                            {{ $field['label'] ?? '' }}
                                            @if(!empty($field['required']))
                                                <em>*</em>
                                            @endif
                                        </span>
                                        <input
                                            type="text"
                                            name="customer_fields[]"
                                            placeholder="{{ $field['placeholder'] ?? '' }}"
                                            value="{{ old('customer_fields.' . $index) }}"
                                            @if(!empty($field['required'])) required @endif
                                        >
                                    </label>
                                @endforeach

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
                                            <img src="{{ asset('assets/1772787522436_1.jpeg') }}" alt="Black" class="color-img">
                                            <span class="color-name">Black</span>
                                        </label>
                                        <label class="color-option">
                                            <input type="checkbox" name="colors[]" value="Golden">
                                            <img src="{{ asset('assets/1772785499339_3.jpeg') }}" alt="Golden" class="color-img">
                                            <span class="color-name">Golden</span>
                                        </label>
                                        <label class="color-option">
                                            <input type="checkbox" name="colors[]" value="Silver">
                                            <img src="{{ asset('assets/silver-color.jpg') }}" alt="Silver" class="color-img">
                                            <span class="color-name">Silver</span>
                                        </label>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <aside class="checkout-summary">
                            <h2>{{ $page->checkout_summary_title }}</h2>

                            <div class="summary-table">
                                <div class="summary-table__head">
                                    <span>Product</span>
                                    <span>Subtotal</span>
                                </div>

                                <div class="summary-product">
                                    <div class="summary-product__item">
                                        <div class="summary-product__thumb">
                                            <img src="{{ $media($page->checkout_product_image) }}" alt="{{ $page->checkout_product_name }}">
                                        </div>
                                        <div class="summary-product__meta">
                                            <strong>{{ $page->checkout_product_name }}</strong>
                                            <span id="summary-qty">&times; 1</span>
                                        </div>
                                    </div>

                                    <div class="summary-price-wrapper">
                                        @if($page->checkout_original_price)
                                            <del class="summary-price-old">{{ $page->checkout_original_price }}</del>
                                        @endif
                                        <strong class="summary-price" id="summary-price">{{ $page->checkout_subtotal }}</strong>
                                    </div>
                                </div>

                                <div class="summary-row">
                                    <span>Subtotal</span>
                                    <strong id="subtotal-display" data-value="{{ $page->checkout_subtotal }}">{{ $page->checkout_subtotal }}</strong>
                                </div>

                                <div class="delivery-options">
                                    @foreach ($page->checkout_delivery_options as $index => $option)
                                        <label class="delivery-option">
                                            <span>{{ $option['label'] ?? '' }}: <strong>{{ $option['price'] ?? '' }}</strong></span>
                                            <input type="radio" name="delivery_option" value="{{ $index }}" 
                                                data-price="{{ $option['price'] ?? '0' }}"
                                                form="checkout-form" @checked((string) old('delivery_option', !empty($option['checked']) ? $index : null) === (string) $index)>
                                        </label>
                                    @endforeach
                                </div>

                                <div class="summary-total">
                                    <span>Total</span>
                                    <strong id="total-display">{{ $page->checkout_total }}</strong>
                                </div>
                            </div>

                            <button type="submit" class="checkout-submit" form="checkout-form">
                                <svg viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M17 9h-1V7a4 4 0 0 0-8 0v2H7a2 2 0 0 0-2 2v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-7a2 2 0 0 0-2-2Zm-7-2a2 2 0 1 1 4 0v2h-4V7Zm7 11H7v-7h10v7Z" />
                                </svg>
                                <span>{{ $page->checkout_button_text }}</span>
                            </button>

                            @if ($page->support_phone)
                                <p class="checkout-help">
                                    অর্ডার করতে কোনো সমস্যা হলে কল করুন:
                                    <a href="tel:{{ preg_replace('/\s+/', '', $page->support_phone) }}">{{ $page->support_phone }}</a>
                                </p>
                            @endif
                        </aside>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="site-footer container">
        <div>
            <strong>{{ $page->footer_brand }}</strong>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Price calculation logic
            const totalDisplay = document.getElementById('total-display');
            const subtotalDisplay = document.getElementById('subtotal-display');
            const deliveryInputs = document.querySelectorAll('input[name="delivery_option"]');

            // Quantity logic
            const qtyInput = document.getElementById('order-qty');
            const minusBtn = document.querySelector('.qty-btn--minus');
            const plusBtn = document.querySelector('.qty-btn--plus');

            if (qtyInput && minusBtn && plusBtn) {
                minusBtn.addEventListener('click', () => {
                    const current = parseInt(qtyInput.value) || 1;
                    if (current > 1) {
                        qtyInput.value = current - 1;
                        updateTotal();
                    }
                });

                plusBtn.addEventListener('click', () => {
                    const current = parseInt(qtyInput.value) || 1;
                    if (current < 4) {
                        qtyInput.value = current + 1;
                        updateTotal();
                    }
                });
            }

            function parsePrice(str) {
                return parseFloat(str.replace(/[^0-9.]/g, '')) || 0;
            }

            function formatPrice(num) {
                return num.toFixed(0) + '৳';
            }

            function updateTotal() {
                const baseSubtotal = parsePrice(subtotalDisplay.getAttribute('data-value'));
                const qty = parseInt(qtyInput.value) || 1;
                const subtotal = baseSubtotal * qty;
                
                let delivery = 0;
                deliveryInputs.forEach(input => {
                    if (input.checked) {
                        delivery = parsePrice(input.getAttribute('data-price'));
                    }
                });

                const summaryQty = document.getElementById('summary-qty');
                const summaryPrice = document.getElementById('summary-price');

                if (subtotalDisplay) subtotalDisplay.innerText = formatPrice(subtotal);
                if (totalDisplay) totalDisplay.innerText = formatPrice(subtotal + delivery);
                if (summaryQty) summaryQty.innerText = '× ' + qty;
                if (summaryPrice) summaryPrice.innerText = formatPrice(subtotal);
            }

            deliveryInputs.forEach(input => {
                input.addEventListener('change', updateTotal);
            });

            updateTotal();

            // Prevent double submission
            const checkoutForm = document.getElementById('checkout-form');
            if (checkoutForm) {
                checkoutForm.addEventListener('submit', function(e) {
                    const submitBtn = document.querySelector('.checkout-submit');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.style.opacity = '0.7';
                        submitBtn.style.cursor = 'not-allowed';
                        const span = submitBtn.querySelector('span');
                        if (span) span.innerText = 'প্রসেসিং হচ্ছে...';
                    }
                });
            }

            // GA4 add_to_cart tracking
            let cartTracked = false;
            document.querySelectorAll('input, button[type=\"submit\"]').forEach(el => {
                el.addEventListener('focus', () => {
                    if (!cartTracked) {
                        window.dataLayer.push({
                            'event': 'add_to_cart',
                            'ecommerce': {
                                'currency': 'BDT',
                                'value': 1199,
                                'items': [{
                                    'item_id': 'sunglass-01',
                                    'item_name': 'High Quality Adjustable Dimming Polarized Sunglass',
                                    'price': 1199,
                                    'quantity': 1
                                }]
                            }
                        });
                        cartTracked = true;
                    }
                });
            });

            // Protection Modal Logic
            const modal = document.getElementById('protection-modal');
            if (modal) {
                setTimeout(() => {
                    modal.classList.add('protection-modal--show');
                }, 100);

                const closeBtn = modal.querySelector('.protection-btn');
                const overlay = modal.querySelector('.protection-overlay');

                [closeBtn, overlay].forEach(el => {
                    if (el) {
                        el.addEventListener('click', () => {
                            modal.classList.remove('protection-modal--show');
                            setTimeout(() => {
                                modal.remove();
                            }, 400);
                        });
                    }
                });
            }
        });
    </script>

    @if ($errors->has('rate_limit'))
        <div class="protection-modal" id="protection-modal">
            <div class="protection-overlay"></div>
            <div class="protection-card">
                <div class="protection-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" stroke-linecap="round" stroke-linejoin="round"/>
                        <circle cx="12" cy="12" r="3" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <h3>Protection Check</h3>
                <p>{{ $errors->first('rate_limit') }}</p>
                <button type="button" class="protection-btn">ঠিক আছে</button>
            </div>
        </div>
    @endif
</body>
</html>
