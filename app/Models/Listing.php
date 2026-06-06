<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use DB;

class Listing extends Model
{
    use HasFactory;

    protected $table = 'listings';

    protected $fillable = ['user_id', 'category_id', 'brand_id', 'unique_id', 'status'];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];
}
