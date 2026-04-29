<?php
// app/Helpers/StoreHelper.php

namespace App\Helpers;

use App\Models\Store;
use App\Models\StoreInventory;
use Illuminate\Support\Facades\Auth;

class StoreHelper
{
    /**
     * Get all stores for current seller
     */
    public static function getSellerStores()
    {
        if (Auth::check() && Auth::user()->menuroles == 'seller') {
            return Store::where('seller_id', Auth::user()->id)->active()->get();
        }
        return collect();
    }
    
    /**
     * Get store inventory for a product across all seller stores
     */
    public static function getProductStoreInventory($listingId)
    {
        if (Auth::check() && Auth::user()->menuroles == 'seller') {
            return StoreInventory::whereHas('store', function($query) {
                $query->where('seller_id', Auth::user()->id);
            })->where('listing_id', $listingId)->get();
        }
        return collect();
    }
    
    /**
     * Get total stock for a product across all seller stores
     */
    public static function getTotalProductStock($listingId)
    {
        return self::getProductStoreInventory($listingId)->sum('quantity');
    }
    
    /**
     * Check if product is available in any store
     */
    public static function isProductAvailable($listingId)
    {
        return self::getTotalProductStock($listingId) > 0;
    }
}