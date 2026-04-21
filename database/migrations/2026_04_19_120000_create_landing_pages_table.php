<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('landing_pages', function (Blueprint $table): void {
            $table->id();
            $table->string('slug')->unique();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('brand_name');
            $table->string('brand_accent');
            $table->text('hero_headline');
            $table->string('hero_image');
            $table->string('floating_cta_label')->default('Order Now');
            $table->string('how_it_works_badge');
            $table->text('how_it_works_title');
            $table->text('how_it_works_description')->nullable();
            $table->json('how_it_works_steps');
            $table->string('benefits_badge');
            $table->text('benefits_title');
            $table->text('benefits_description')->nullable();
            $table->string('benefits_image')->nullable();
            $table->json('benefits_items');
            $table->string('features_badge');
            $table->text('features_title');
            $table->string('features_image')->nullable();
            $table->json('feature_items');
            $table->string('snapshot_badge');
            $table->json('snapshot_items');
            $table->string('gallery_badge');
            $table->text('gallery_title');
            $table->text('gallery_description')->nullable();
            $table->json('gallery_items');
            $table->string('use_cases_badge');
            $table->text('use_cases_title');
            $table->string('use_cases_image')->nullable();
            $table->json('use_case_items');
            $table->string('comparison_badge');
            $table->text('comparison_title');
            $table->string('comparison_regular_title');
            $table->json('comparison_regular_items');
            $table->string('comparison_highlight_title');
            $table->json('comparison_highlight_items');
            $table->string('faq_badge');
            $table->text('faq_title');
            $table->json('faq_items');
            $table->string('checkout_title');
            $table->string('checkout_summary_title');
            $table->string('checkout_button_text');
            $table->string('support_phone')->nullable();
            $table->json('checkout_fields');
            $table->string('checkout_product_name');
            $table->string('checkout_product_image');
            $table->string('checkout_subtotal');
            $table->json('checkout_delivery_options');
            $table->string('checkout_total');
            $table->string('footer_brand');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_pages');
    }
};
