<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderItemOption extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['order_item_id', 'product_option_item_id', 'name', 'price', 'meta'];

    protected $casts = [
        'price' => 'decimal:2',
        'meta' => 'array',
    ];

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function productOptionItem()
    {
        return $this->belongsTo(ProductOptionItem::class);
    }
}
