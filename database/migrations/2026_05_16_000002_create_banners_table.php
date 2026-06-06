<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('banners')) {
            Schema::create('banners', function (Blueprint $table) {
                $table->id();
                $table->string('image');
                $table->string('title')->nullable();
                $table->string('redirect_type', 50)->default('url')
                      ->comment('url | category | product');
                $table->string('redirect_value')->default('');
                $table->unsignedTinyInteger('display_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};
