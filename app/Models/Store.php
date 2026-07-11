<?php
// app/Models/Store.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Store extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'stores';

    protected $fillable = [
        'seller_id', 'store_name', 'store_slug', 'description', 'store_email',
        'store_phone', 'address', 'city', 'state', 'country', 'pincode',
        'latitude', 'longitude', 'logo', 'cover_image', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    // Boot method to generate slug
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($store) {
            $store->store_slug = Str::slug($store->store_name . '-' . uniqid());
        });
    }

    // Relationships
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function employees()
    {
        return $this->hasMany(StoreEmployee::class);
    }

    public function inventory()
    {
        return $this->hasMany(StoreInventory::class);
    }

    public function listings()
    {
        return $this->belongsToMany(Listing::class, 'store_inventory', 'store_id', 'listing_id')
                    ->withPivot('quantity', 'price', 'mrp', 'is_available')
                    ->withTimestamps();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeNearby($query, $latitude, $longitude, $radius = 10)
    {
        return $query->selectRaw("*, ( 6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * sin( radians( latitude ) ) ) ) AS distance", 
            [$latitude, $longitude, $latitude])
            ->having('distance', '<', $radius)
            ->orderBy('distance');
    }

    // Helper methods
    public function hasProduct($listingId)
    {
        return $this->inventory()->where('listing_id', $listingId)->exists();
    }

    public function getProductStock($listingId)
    {
        $inventory = $this->inventory()->where('listing_id', $listingId)->first();
        return $inventory ? $inventory->quantity : 0;
    }

    public function updateStock($listingId, $quantity, $operation = 'add')
    {
        $inventory = $this->inventory()->firstOrCreate(
            ['listing_id' => $listingId],
            ['quantity' => 0, 'store_id' => $this->id]
        );

        if ($operation === 'add') {
            $inventory->quantity += $quantity;
        } elseif ($operation === 'subtract') {
            $inventory->quantity -= $quantity;
        } elseif ($operation === 'set') {
            $inventory->quantity = $quantity;
        }

        $inventory->is_available = $inventory->quantity > 0;
        return $inventory->save();
    }
}