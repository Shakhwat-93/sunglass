<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>অর্ডার সফল হয়েছে | {{ $page->brand_name }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Noto+Sans+Bengali:wght@400;500;600;700;800&display=swap" rel="stylesheet">
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
            'event': 'purchase',
            'customer_name': '{{ session('customer_name') }}',
            'customer_address': '{{ session('customer_address') }}',
            'customer_phone': '{{ session('customer_phone') }}',
            'ecommerce': {
                'transaction_id': '{{ session('order_id') }}',
                'value': {{ session('order_total', 1199) }},
                'currency': 'BDT',
                'items': [{
                    'item_id': 'sunglass-01',
                    'item_name': 'High Quality Adjustable Dimming Polarized Sunglass',
                    'price': {{ session('order_total', 1199) }},
                    'quantity': 1
                }]
            }
        });
    </script>
    <style>
        :root {
            --success-color: #10b981;
        }

        body {
            background-color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            font-family: 'Manrope', 'Noto Sans Bengali', sans-serif;
        }

        .success-container {
            text-align: center;
            padding: 3rem;
            max-width: 500px;
            width: 90%;
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.05);
            animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .success-icon {
            width: 80px;
            height: 80px;
            background: rgba(16, 185, 129, 0.1);
            color: var(--success-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            animation: scaleIn 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        @keyframes scaleIn {
            from { transform: scale(0); }
            to { transform: scale(1); }
        }

        .success-icon svg {
            width: 40px;
            height: 40px;
        }

        h1 {
            font-size: 2rem;
            font-weight: 800;
            color: #111827;
            margin-bottom: 1rem;
            line-height: 1.2;
        }

        p {
            font-size: 1.1rem;
            color: #6b7280;
            line-height: 1.6;
            margin-bottom: 2.5rem;
        }

        .btn-home {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #111827;
            color: #ffffff;
            padding: 1rem 2rem;
            border-radius: 12px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            gap: 0.5rem;
        }

        .btn-home:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            background: #000;
        }

        .btn-home svg {
            width: 20px;
            height: 20px;
        }

        .brand-logo {
            margin-bottom: 3rem;
            display: block;
            text-decoration: none;
            color: #111827;
            font-size: 1.5rem;
            font-weight: 800;
        }

        .brand-logo span {
            color: #6366f1; /* Using a subtle indigo accent */
        }
    </style>
</head>
<body>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-PMHNV9HJ"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
    <div class="success-container">
        <a href="/" class="brand-logo">
            <span>{{ $page->brand_accent }}</span>{{ \Illuminate\Support\Str::after($page->brand_name, $page->brand_accent) }}
        </a>

        <div class="success-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
        </div>

        <h1>অর্ডার সফল হয়েছে!</h1>
        <p>আপনার অর্ডারটি আমরা সফলভাবে গ্রহণ করেছি। খুব শীঘ্রই আমাদের একজন প্রতিনিধি আপনার সাথে যোগাযোগ করবেন।</p>

        <a href="/" class="btn-home">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                <polyline points="9 22 9 12 15 12 15 22"></polyline>
            </svg>
            <span>হোম পেজে ফিরে যান</span>
        </a>
    </div>
</body>
</html>
