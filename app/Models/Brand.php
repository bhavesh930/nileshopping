<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Brand extends Model
{
    use HasFactory;

    protected $table = 'brands';

    protected $fillable = ['seller_id', 'brand_name', 'brand_slug'];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];
}
