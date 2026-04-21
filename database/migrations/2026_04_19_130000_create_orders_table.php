<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table): void {
            $table->id();
            $table->string('customer_name');
            $table->text('shipping_address');
            $table->string('phone', 32);
            $table->string('product_name');
            $table->string('subtotal');
            $table->string('delivery_label');
            $table->string('delivery_price');
            $table->string('total');
            $table->string('status')->default('new');
            $table->string('source')->default('landing-page');
            $table->json('items');
            $table->json('customer_fields');
            $table->text('admin_note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
