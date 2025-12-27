<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Product extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    protected $fillable = [
        'category_id',
        'subcategory_id',
        'name',
        'slug',
        'description',
        'price',
        'currency',
        'available',
        'meta',
    ];

    protected $casts = [
        'available' => 'boolean',
        'meta' => 'array',
        'price' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class);
    }

    /**
     * Permite asociar el mismo producto a múltiples sucursales (restaurants).
     */
    public function restaurants()
    {
        return $this->belongsToMany(Restaurant::class, 'product_restaurant')
            ->withTimestamps();
    }

    public function options()
    {
        return $this->hasMany(ProductOption::class);
    }

    /**
     * Promociones aplicadas a este producto.
     */
    public function promotions()
    {
        return $this->belongsToMany(Promotion::class, 'promotion_product')
            ->withTimestamps();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images');
    }
}
