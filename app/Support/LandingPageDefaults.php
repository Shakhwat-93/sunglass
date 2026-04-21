<?php

namespace App\Support;

class LandingPageDefaults
{
    public static function data(): array
    {
        return [
            'slug' => 'home',
            'meta_title' => 'Chinatownbd | Adjustable ND Dimming Polarized Sunglasses',
            'meta_description' => 'Chinatownbd এর adjustable dimming polarized sunglasses। bright sun থেকে evening light পর্যন্ত clear vision, lower glare, and premium outdoor style.',
            'brand_name' => 'Chinatownbd',
            'brand_accent' => 'Chinatown',
            'hero_headline' => '"এক চশমায় রোদের কন্ট্রোল, পরিষ্কার ভিশন আর প্রিমিয়াম স্টাইল"',
            'hero_image' => 'assets/hero.webp',
            'floating_cta_label' => 'অর্ডার করুন',
            'how_it_works_badge' => 'কিভাবে কাজ করে',
            'how_it_works_title' => 'একটা simple rotation-এই বদলে যাবে আপনার outdoor view',
            'how_it_works_description' => 'ND-style rotating frame camera filter-এর মতো কাজ করে। outer ring ঘুরিয়ে brightness pass-through কম বা বেশি করতে পারবেন।',
            'how_it_works_steps' => [
                [
                    'step' => '01',
                    'title' => 'MIN Mode',
                    'description' => 'Cloudy sky, shade, dawn বা dusk-এ brighter setup।',
                    'image' => 'assets/1772785511950_2.jpeg',
                ],
                [
                    'step' => '02',
                    'title' => 'Mid Control',
                    'description' => 'Daily commute, travel, city walk, normal sunlight-এর জন্য balanced tint।',
                    'image' => 'assets/1772785499339_3.jpeg',
                ],
                [
                    'step' => '03',
                    'title' => 'MAX Protection',
                    'description' => 'Hard sunlight, road glare, water reflection, riding ও fishing-এর জন্য stronger shade।',
                    'image' => 'assets/1770442115570_3.webp',
                ],
            ],
            'benefits_badge' => 'মূল সুবিধাগুলো',
            'benefits_title' => 'কেন এই sunglasses সাধারণ model-এর চেয়ে অনেক বেশি practical',
            'benefits_description' => 'Fixed tint sunglasses যেখানে এক environment-এ useful, সেখানে এই model multiple light condition-এ adapt করে।',
            'benefits_image' => 'assets/1772787522436_1.jpeg',
            'benefits_items' => [
                [
                    'title' => 'এক চশমায় বহু পরিবেশ',
                    'description' => 'দিনের কড়া রোদ, cloudy light, road reflection, fishing glare, travel stop বা evening transition। একই pair adjust করে ব্যবহার করা যায়।',
                    'featured' => true,
                ],
                [
                    'title' => 'চোখে চাপ কমে',
                    'description' => 'Polarized anti-glare lens harsh reflection soft করে, longer use-এ view calmer লাগে।',
                    'featured' => false,
                ],
                [
                    'title' => 'UV400 Protection',
                    'description' => 'Outdoor sun exposure-এ চোখের জন্য stronger safety layer দেয়।',
                    'featured' => false,
                ],
                [
                    'title' => 'প্রিমিয়াম বিল্ড + স্টাইল',
                    'description' => 'Aluminum-magnesium alloy frame cheap plastic feel থেকে অনেক বেশি premium impression দেয়।',
                    'featured' => false,
                ],
                [
                    'title' => 'Lightweight Comfort',
                    'description' => 'Approx. 33g হওয়ায় daily use, riding, traveling-এ heavy feel হয় না।',
                    'featured' => false,
                ],
            ],
            'features_badge' => 'প্রধান বৈশিষ্ট্য',
            'features_title' => 'Premium optics, premium fit, premium perception',
            'features_image' => 'assets/1772785312579_1.jpeg',
            'feature_items' => [
                [
                    'title' => 'Multi-Gear Manual Adjustment',
                    'description' => 'Camera lens ND filter-inspired rotating ring দিয়ে brightness বা darkness instantly control করা যায়।',
                ],
                [
                    'title' => 'Polarized Anti-Glare HD Lens',
                    'description' => 'Road, water, glass reflection কমিয়ে clearer, more stable vision দেয়।',
                ],
                [
                    'title' => 'Premium Metal Round Full Frame',
                    'description' => 'Modern round silhouette + durable alloy build product-টাকে visually more unique করে।',
                ],
                [
                    'title' => 'Metal Nose Pad Comfort Fit',
                    'description' => 'Balanced nose support দেয়, pressure mark বা discomfort minimise করে।',
                ],
                [
                    'title' => 'Unisex Multi-Scene Use',
                    'description' => 'পুরুষ ও মহিলা উভয়ের জন্য suitable, বিশেষ করে driving, fishing, riding, travel use-case-এ।',
                ],
            ],
            'snapshot_badge' => 'Product Snapshot',
            'snapshot_items' => [
                ['label' => 'Product', 'value' => 'Adjustable Dimming Polarized Sunglasses'],
                ['label' => 'Control', 'value' => '9 Level ND2-400 inspired dimming feel'],
                ['label' => 'Lens', 'value' => 'Polarized HD + UV400'],
                ['label' => 'Weight', 'value' => 'Approx. 33g'],
                ['label' => 'Frame', 'value' => 'Aluminum-magnesium alloy'],
                ['label' => 'Design', 'value' => 'Full frame round, unisex'],
                ['label' => 'Included', 'value' => 'Glasses case'],
            ],
            'gallery_badge' => 'গ্যালারি',
            'gallery_title' => 'Original product assets থেকে premium presentation',
            'gallery_description' => 'বাস্তব product perception strong করার জন্য clean image-led gallery রাখা হয়েছে।',
            'gallery_items' => [
                ['image' => 'assets/1772787522436_1.jpeg', 'alt' => 'ND sunglasses with premium case', 'featured' => true],
                ['image' => 'assets/1772785499339_3.jpeg', 'alt' => 'Reflective lens sunglasses detail', 'featured' => false],
                ['image' => 'assets/1772785511950_2.jpeg', 'alt' => 'Adjustable sunglasses closeup', 'featured' => false],
                ['image' => 'assets/1772785312579_1.jpeg', 'alt' => 'Premium sunglasses side detail', 'featured' => false],
                ['image' => 'assets/1772785413954_3.jpeg', 'alt' => 'Polarized sunglasses product detail', 'featured' => false],
                ['image' => 'assets/1772785541355_5.jpeg', 'alt' => 'Adjustable lens frame detail', 'featured' => false],
                ['image' => 'assets/1772785636346_5.jpeg', 'alt' => 'Dimming sunglasses package visual', 'featured' => false],
                ['image' => 'assets/1772787466471_2.jpeg', 'alt' => 'Premium case image', 'featured' => false],
                ['image' => 'assets/1770442115570_3.webp', 'alt' => 'Sunglasses product hero alternate', 'featured' => false],
            ],
            'use_cases_badge' => 'ব্যবহার ক্ষেত্র',
            'use_cases_title' => 'যারা regular outdoor-এ থাকেন, তাদের জন্য এটা সবচেয়ে useful',
            'use_cases_image' => 'assets/1770442115570_3.webp',
            'use_case_items' => [
                ['title' => 'Driving', 'description' => 'Windshield reflection ও road glare কমিয়ে view cleaner করে।'],
                ['title' => 'Fishing', 'description' => 'Water surface glare reduce হওয়ায় detail দেখতে সহজ হয়।'],
                ['title' => 'Bike Riding', 'description' => 'Changing sunlight condition-এ tint adjust করা easy থাকে।'],
                ['title' => 'Travel', 'description' => 'একাধিক sunglasses carry না করে one-pair solution পাওয়া যায়।'],
            ],
            'comparison_badge' => 'Why Upgrade',
            'comparison_title' => 'Regular sunglasses বনাম Chinatownbd Adjustable ND',
            'comparison_regular_title' => 'Regular Sunglasses',
            'comparison_regular_items' => [
                ['label' => 'Brightness Control', 'value' => 'Fixed tint'],
                ['label' => 'Glare Handling', 'value' => 'Basic lens'],
                ['label' => 'Versatility', 'value' => 'One light condition'],
                ['label' => 'Build Quality', 'value' => 'Often plastic-heavy'],
                ['label' => 'Comfort', 'value' => 'Average'],
            ],
            'comparison_highlight_title' => 'Chinatownbd ND Model',
            'comparison_highlight_items' => [
                ['label' => 'Brightness Control', 'value' => 'Adjustable multi-level control'],
                ['label' => 'Glare Handling', 'value' => 'Polarized anti-glare clarity'],
                ['label' => 'Versatility', 'value' => 'Sun, cloud, ride, travel, fishing'],
                ['label' => 'Build Quality', 'value' => 'Premium alloy frame'],
                ['label' => 'Comfort', 'value' => '33g lightweight + metal nose pad'],
            ],
            'faq_badge' => 'FAQ',
            'faq_title' => 'অর্ডার করার আগে যা জানা দরকার',
            'faq_items' => [
                [
                    'question' => 'এই sunglasses কি সত্যিই brightness adjust করা যায়?',
                    'answer' => 'হ্যাঁ। rotating ND-style frame ঘুরিয়ে tint level adjust করা যায়, ফলে different light conditions-এ better control পাওয়া যায়।',
                ],
                [
                    'question' => 'এটা কি polarized?',
                    'answer' => 'হ্যাঁ, polarized anti-glare lens ব্যবহার করা হয়েছে, যা reflection ও glare কমাতে সাহায্য করে।',
                ],
                [
                    'question' => 'UV protection আছে?',
                    'answer' => 'হ্যাঁ, UV400 feature outdoor sunlight exposure-এ চোখের জন্য better protection দেয়।',
                ],
                [
                    'question' => 'পুরুষ ও মহিলা দুইজনেই ব্যবহার করতে পারবে?',
                    'answer' => 'হ্যাঁ, এটা unisex design। round full-frame style পুরুষ ও নারী উভয়ের জন্য suitable।',
                ],
                [
                    'question' => 'দীর্ঘক্ষণ পরলে heavy লাগবে?',
                    'answer' => 'না। approximate 33g weight হওয়ায় long-wear use-এও comfortable।',
                ],
            ],
            'checkout_title' => 'Billing & Shipping',
            'checkout_summary_title' => 'Your order',
            'checkout_button_text' => 'অর্ডার কনফার্ম করুন',
            'support_phone' => '+8801942-212267',
            'checkout_fields' => [
                ['label' => 'আপনার নাম লিখুন', 'placeholder' => 'e.g. Hasan Mahmud', 'required' => true],
                ['label' => 'আপনার ঠিকানা এলাকা, থানা, জেলা লিখুন', 'placeholder' => 'e.g. House 12, Road 4, Dhanmondi, Dhaka', 'required' => true],
                ['label' => 'মোবাইল নাম্বার', 'placeholder' => '01XXXXXXXXX', 'required' => true],
            ],
            'checkout_product_name' => 'High Quality Adjustable Dimming Polarized Sunglass',
            'checkout_product_image' => 'assets/1772787522436_1.jpeg',
            'checkout_subtotal' => '1199৳',
            'checkout_original_price' => '2200৳',
            'checkout_delivery_options' => [
                ['label' => 'ঢাকার বাইরে ডেলিভারি চার্জ', 'price' => '130.00৳', 'checked' => false],
                ['label' => 'ঢাকার ভিতরে ডেলিভারি চার্জ', 'price' => '60.00৳', 'checked' => true],
            ],
            'checkout_total' => '1259৳',
            'footer_brand' => 'Chinatownbd',
        ];
    }
}
