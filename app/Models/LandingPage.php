<?php

namespace App\Models;

use App\Support\LandingPageDefaults;
use Illuminate\Database\Eloquent\Model;
use Throwable;

class LandingPage extends Model
{
    protected $fillable = [
        'slug',
        'meta_title',
        'meta_description',
        'brand_name',
        'brand_accent',
        'hero_headline',
        'hero_image',
        'floating_cta_label',
        'how_it_works_badge',
        'how_it_works_title',
        'how_it_works_description',
        'how_it_works_steps',
        'benefits_badge',
        'benefits_title',
        'benefits_description',
        'benefits_image',
        'benefits_items',
        'features_badge',
        'features_title',
        'features_image',
        'feature_items',
        'snapshot_badge',
        'snapshot_items',
        'gallery_badge',
        'gallery_title',
        'gallery_description',
        'gallery_items',
        'use_cases_badge',
        'use_cases_title',
        'use_cases_image',
        'use_case_items',
        'comparison_badge',
        'comparison_title',
        'comparison_regular_title',
        'comparison_regular_items',
        'comparison_highlight_title',
        'comparison_highlight_items',
        'faq_badge',
        'faq_title',
        'faq_items',
        'checkout_title',
        'checkout_summary_title',
        'checkout_button_text',
        'support_phone',
        'checkout_fields',
        'checkout_product_name',
        'checkout_product_image',
        'checkout_subtotal',
        'checkout_delivery_options',
        'checkout_total',
        'checkout_original_price',
        'footer_brand',
    ];

    protected function casts(): array
    {
        return [
            'how_it_works_steps' => 'array',
            'benefits_items' => 'array',
            'feature_items' => 'array',
            'snapshot_items' => 'array',
            'gallery_items' => 'array',
            'use_case_items' => 'array',
            'comparison_regular_items' => 'array',
            'comparison_highlight_items' => 'array',
            'faq_items' => 'array',
            'checkout_fields' => 'array',
            'checkout_delivery_options' => 'array',
        ];
    }

    public static function home(): self
    {
        try {
            return static::query()->firstOrCreate(
                ['slug' => 'home'],
                LandingPageDefaults::data(),
            );
        } catch (Throwable) {
            $page = new static();
            $page->forceFill(LandingPageDefaults::data());

            return $page;
        }
    }
}
