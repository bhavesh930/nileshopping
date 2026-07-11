<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoreInventoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Disable foreign key checks temporarily
        Schema::disableForeignKeyConstraints();

        Schema::create('store_inventory', function (Blueprint $table) {
            $table->unsignedBigInteger('store_id');
            $table->unsignedBigInteger('listing_id');
            $table->integer('quantity')->default(0);
            $table->decimal('price', 10, 2)->nullable(); // Store-specific price override
            $table->decimal('mrp', 10, 2)->nullable(); // Store-specific MRP override
            $table->boolean('is_available')->default(true);
            $table->timestamps();
            
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            //$table->foreign('listing_id')->references('id')->on('listings')->onDelete('cascade');
            $table->unique(['store_id', 'listing_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('store_inventory');
    }
}
