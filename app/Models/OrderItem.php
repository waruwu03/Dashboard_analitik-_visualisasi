<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $table = 'order_items';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = null;
    public $timestamps = true;
    protected $fillable = [
        'order_id',
        'order_item_id',
        'product_id',
        'seller_id',
        'shipping_limit_date',
        'price',
        'freight_value',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
}
