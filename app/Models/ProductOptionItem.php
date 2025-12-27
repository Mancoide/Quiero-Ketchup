<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductOptionItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['product_option_id', 'name', 'price', 'meta'];

    protected $casts = [
        'price' => 'decimal:2',
        'meta' => 'array',
    ];

    public function option()
    {
        return $this->belongsTo(ProductOption::class, 'product_option_id');
    }
}
