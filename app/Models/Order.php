<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'customer_name',
        'shipping_address',
        'phone',
        'product_name',
        'subtotal',
        'delivery_label',
        'delivery_price',
        'total',
        'status',
        'source',
        'items',
        'customer_fields',
        'admin_note',
    ];

    protected function casts(): array
    {
        return [
            'items' => 'array',
            'customer_fields' => 'array',
        ];
    }
}
