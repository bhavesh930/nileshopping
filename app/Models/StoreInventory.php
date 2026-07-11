<?php
// app/Models/StoreInventory.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreInventory extends Model
{
    use HasFactory;

    protected $table = 'store_inventory';

    protected $fillable = [
        'store_id', 'listing_id', 'quantity', 'price', 'mrp', 'is_available'
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'quantity' => 'integer',
    ];

    // Relationships
    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }
}