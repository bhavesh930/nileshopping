<?php
// app/Models/StoreEmployee.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StoreEmployee extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'store_employees';

    protected $fillable = [
        'store_id', 'user_id', 'employee_code', 'designation', 'permissions', 'is_active'
    ];

    protected $casts = [
        'permissions' => 'array',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Helper methods for permissions
    public function hasPermission($permission)
    {
        if (!$this->permissions) {
            return false;
        }
        
        $permissions = is_array($this->permissions) ? $this->permissions : json_decode($this->permissions, true);
        return in_array($permission, $permissions);
    }

    public function canManageProducts()
    {
        return $this->hasPermission('manage_products');
    }

    public function canManageInventory()
    {
        return $this->hasPermission('manage_inventory');
    }

    public function canManageOrders()
    {
        return $this->hasPermission('manage_orders');
    }
}