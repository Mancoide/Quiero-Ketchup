<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Restaurant extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    protected $fillable = [
        'name', 'slug', 'description', 'address_id', 'phone', 'email', 'status', 'settings', 'meta'
    ];

    protected $casts = [
        'settings' => 'array',
        'meta' => 'array',
    ];

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    /**
     * Productos disponibles en esta sucursal.
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_restaurant')
            ->withTimestamps();
    }

    public function promotions()
    {
        return $this->hasMany(Promotion::class);
    }

    public function scopedPromotions()
    {
        return $this->belongsToMany(Promotion::class, 'promotion_restaurant')
            ->withTimestamps();
    }

    public function locations()
    {
        return $this->hasMany(Location::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')->singleFile();
    }
}
