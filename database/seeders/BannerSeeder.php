<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BannerSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('banners')->truncate();

        $banners = [
            [
                'image'         => 'banner_1.jpg',
                'title'         => 'Season Sale – Up to 50% Off',
                'redirect_type' => 'url',
                'redirect_value' => '/offers/season-sale',
                'display_order' => 1,
                'is_active'     => true,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'image'         => 'banner_2.jpg',
                'title'         => 'New Arrivals – Women\'s Collection',
                'redirect_type' => 'category',
                'redirect_value' => 'women',
                'display_order' => 2,
                'is_active'     => true,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'image'         => 'banner_3.jpg',
                'title'         => 'Kids Essentials – Starting ₹99',
                'redirect_type' => 'category',
                'redirect_value' => 'kids',
                'display_order' => 3,
                'is_active'     => true,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];

        DB::table('banners')->insert($banners);
    }
}
