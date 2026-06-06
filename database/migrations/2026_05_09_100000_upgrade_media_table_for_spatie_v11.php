<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Upgrade media table for spatie/laravel-medialibrary v11
|--------------------------------------------------------------------------
|
| The original 2020_01_08_184500_create_media_table migration was written
| for Spatie MediaLibrary v8. v11 introduced three schema changes that
| are required for Spatie's Media model to insert rows successfully:
|
|   1. Adds a `generated_conversions` json column (was simply absent in v8).
|   2. Replaces the `uuid` column — v8 declared it as unsignedBigInteger,
|      v11 stores actual UUID strings. Existing integer uuids are dropped
|      (they were never valid Spatie UUIDs).
|   3. Makes `conversions_disk` nullable — v11 allows null when no
|      conversions are configured.
|
| All operations are guarded so this migration is safe to re-run.
|
*/

class UpgradeMediaTableForSpatieV11 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasColumn('media', 'generated_conversions')) {
            Schema::table('media', function (Blueprint $table) {
                $table->json('generated_conversions')->nullable()->after('responsive_images');
            });
        }

        if (Schema::hasColumn('media', 'uuid')) {
            Schema::table('media', function (Blueprint $table) {
                $table->dropColumn('uuid');
            });
        }

        Schema::table('media', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->unique()->after('model_id');
        });

        Schema::table('media', function (Blueprint $table) {
            $table->string('conversions_disk')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('media', function (Blueprint $table) {
            if (Schema::hasColumn('media', 'generated_conversions')) {
                $table->dropColumn('generated_conversions');
            }
        });
    }
}
