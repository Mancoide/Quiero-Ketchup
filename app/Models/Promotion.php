<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Promotion extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['code', 'type', 'value', 'starts_at', 'ends_at', 'meta'];

    protected $casts = [
        'value' => 'decimal:2',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'meta' => 'array',
    ];

    /**
     * Promos por producto (p.ej. descuento o 2x1 en un producto).
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'promotion_product')
            ->withTimestamps();
    }

    /**
     * Promos aplicables a múltiples sucursales.
     */
    public function restaurants()
    {
        return $this->belongsToMany(Restaurant::class, 'promotion_restaurant')
            ->withTimestamps();
    }
}
