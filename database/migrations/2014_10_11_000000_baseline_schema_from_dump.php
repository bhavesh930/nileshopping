<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Baseline schema from dump
|--------------------------------------------------------------------------
|
| Creates the 13 tables that exist in the live nile_db but were never
| captured as Laravel migrations (they were imported from a SQL dump).
| Each Schema::create() is wrapped in a hasTable() guard so this migration
| is idempotent — safe to run on the live DB (skips everything) and on a
| fresh DB (creates everything).
|
| Notes on intentional differences from the dump:
| - `listings` is created WITHOUT `store_id`, `is_global`, or the FK to
|   `stores` — those are added by 2026_03_29_060614_add_store_id_to_listings_table.
| - `listingadditions.answer` is created nullable; the later migration
|   2022_02_22_140132_makeanswerdefaultnullinlistingdata_table is then a
|   no-op rather than failing.
|
*/

class BaselineSchemaFromDump extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('attributes')) {
            Schema::create('attributes', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('category_id');
                $table->string('title')->nullable();
                $table->integer('flag')->default(1)->comment('1=Active, 2=Deactive');
                $table->dateTime('created_at')->useCurrent();
                $table->dateTime('updated_at')->useCurrent();
                $table->dateTime('deleted_at')->nullable();
            });
        }

        if (! Schema::hasTable('brandrequests')) {
            Schema::create('brandrequests', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('user_id');
                $table->string('brand_name');
                $table->string('brand_logo')->nullable();
                $table->integer('offline_market')->default(1)->comment('1=Yes, 2=No');
                $table->string('website_link')->nullable();
                $table->string('sell_product_brand')->nullable();
                $table->string('mrp_tag')->nullable();
                $table->integer('brand_owner')->default(1)->comment('1=Yes, 2=No');
                $table->string('trademark_doc')->nullable();
                $table->string('document_type')->nullable();
                $table->dateTime('created_at')->useCurrent();
                $table->dateTime('updated_at')->nullable();
                $table->dateTime('deleted_at')->nullable();
                $table->integer('status')->default(2)->comment('1=Accept, 2=Pending, 3=Rejected');
            });
        }

        if (! Schema::hasTable('brands')) {
            Schema::create('brands', function (Blueprint $table) {
                $table->increments('brand_id');
                $table->integer('seller_id');
                $table->string('brand_name');
                $table->dateTime('created_at')->useCurrent();
                $table->dateTime('updated_at')->nullable();
                $table->dateTime('deleted_at')->nullable();
            });
        }

        if (! Schema::hasTable('carts')) {
            Schema::create('carts', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('user_id');
                $table->integer('seller_id')->nullable();
                $table->integer('product_id')->comment('listingdatas id');
                $table->string('product_attribute')->nullable();
                $table->decimal('price', 10, 2)->nullable();
                $table->integer('quantity')->nullable();
                $table->integer('delivery_type')->default(1)->comment('Home=1, Pickup = 2');
                $table->integer('status')->default(1)->comment('1=Pending(still in cart), 2=Procced');
                $table->dateTime('created_at')->useCurrent();
                $table->dateTime('updated_at')->useCurrent();
                $table->dateTime('deleted_at')->nullable();
            });
        }

        if (! Schema::hasTable('listing_sizechart')) {
            Schema::create('listing_sizechart', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('listing_id');
                $table->string('sizeFor', 150)->nullable();
                $table->string('size', 20);
                $table->integer('quantity')->nullable();
                $table->string('brand_size', 50)->nullable();
                $table->decimal('price', 10, 2);
                $table->text('detail')->nullable();
                $table->dateTime('created_at')->useCurrent();
                $table->dateTime('updated_at')->nullable();
                $table->dateTime('deleted_at')->nullable();
            });
        }

        if (! Schema::hasTable('listingadditions')) {
            Schema::create('listingadditions', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('listing_id')->default(1);
                $table->integer('category_id');
                $table->integer('question_id');
                $table->text('answer')->nullable();
                $table->dateTime('created_at')->useCurrent();
                $table->dateTime('updated_at')->nullable();
                $table->dateTime('deleted_at')->nullable();
            });
        }

        if (! Schema::hasTable('listingdatas')) {
            Schema::create('listingdatas', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('listing_id')->default(1);
                $table->text('product_name')->nullable();
                $table->string('sku', 100)->nullable();
                $table->string('status', 50)->nullable();
                $table->decimal('mrp', 10, 2)->nullable();
                $table->decimal('selling_price', 10, 2)->nullable();
                $table->string('fullfilment', 100)->nullable();
                $table->string('procurement_type', 100)->nullable();
                $table->string('procurement_sla', 100)->nullable();
                $table->string('stock', 50)->nullable();
                $table->string('shipping_provider')->nullable();
                $table->string('local_delivery_charge', 150)->nullable();
                $table->string('zonal_delivery_charge', 150)->nullable();
                $table->string('national_delivery_charge', 150)->nullable();
                $table->string('package_weight', 150)->nullable();
                $table->string('package_length', 150)->nullable();
                $table->string('package_breadth', 150)->nullable();
                $table->string('package_height', 150)->nullable();
                $table->string('hsn', 50)->nullable();
                $table->string('luxury_cess', 100)->nullable();
                $table->string('tax_code', 100)->nullable();
                $table->text('country_origin')->nullable();
                $table->text('manufacturer_detail')->nullable();
                $table->text('packer_detail')->nullable();
                $table->text('importer_detail')->nullable();
                $table->string('modal_number')->nullable();
                $table->string('brand_color')->nullable();
                $table->string('primary_material_type')->nullable();
                $table->string('size')->nullable();
                $table->string('color')->nullable();
                $table->string('suitable_for')->nullable();
                $table->string('primary_material')->nullable();
                $table->text('delivery_condition')->nullable();
                $table->string('age_group')->nullable();
                $table->string('product_width')->nullable();
                $table->string('product_height')->nullable();
                $table->string('product_depth')->nullable();
                $table->string('product_weight')->nullable();
                $table->text('warranty_summary')->nullable();
                $table->text('covered_warranty')->nullable();
                $table->text('not_covered_warranty')->nullable();
                $table->dateTime('created_at')->useCurrent();
                $table->dateTime('updated_at')->nullable();
                $table->dateTime('deleted_at')->nullable();
            });
        }

        if (! Schema::hasTable('listingphotos')) {
            Schema::create('listingphotos', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('listing_id');
                $table->string('image_1')->nullable();
                $table->string('image_2')->nullable();
                $table->string('image_3')->nullable();
                $table->string('image_4')->nullable();
                $table->string('image_5')->nullable();
                $table->dateTime('created_at')->useCurrent()->nullable();
                $table->dateTime('updated_at')->nullable();
                $table->dateTime('deleted_at')->nullable();
            });
        }

        if (! Schema::hasTable('listings')) {
            Schema::create('listings', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('user_id');
                // store_id, is_global, and the stores FK are added by
                // 2026_03_29_060614_add_store_id_to_listings_table.
                $table->integer('category_id');
                $table->integer('brand_id');
                $table->string('unique_id', 50)->nullable();
                $table->integer('status')->default(0)->comment('0=draft, 1=Archive, 2=Admin Request, 3=Active');
                $table->text('menu_id')->nullable();
                $table->text('hastags')->nullable();
                $table->dateTime('created_at')->useCurrent();
                $table->dateTime('updated_at')->nullable();
                $table->dateTime('deleted_at')->nullable();
            });
        }

        if (! Schema::hasTable('orders')) {
            Schema::create('orders', function (Blueprint $table) {
                $table->increments('id');
                $table->text('order_id')->nullable();
                $table->integer('user_id');
                $table->text('cart_id')->nullable();
                $table->integer('order_status')->default(1)->comment('1=Processing,2=Pending,3=Completed,4=Cancelled');
                $table->text('transaction_id')->nullable();
                $table->text('transaction_status')->nullable();
                $table->integer('address_id')->nullable();
                $table->integer('payment_mode')->default(1)->comment('1=Cash, 2=Online');
                $table->decimal('total_amount', 10, 2);
                $table->decimal('discount', 10, 2);
                $table->decimal('delivery_charge', 10, 2);
                $table->decimal('gst', 10, 2);
                $table->decimal('grand_total', 10, 2);
                $table->dateTime('created_at')->useCurrent();
                $table->dateTime('updated_at')->useCurrent();
                $table->dateTime('deleted_at')->nullable();
            });
        }

        if (! Schema::hasTable('reviews')) {
            Schema::create('reviews', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('order_id');
                $table->integer('product_id')->comment('listingdatas id');
                $table->integer('user_id');
                $table->decimal('rating', 10, 2)->nullable();
                $table->text('review')->nullable();
                $table->dateTime('created_at')->useCurrent();
                $table->dateTime('updated_at')->useCurrent();
                $table->dateTime('deleted_at')->nullable();
            });
        }

        if (! Schema::hasTable('sellers')) {
            Schema::create('sellers', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('user_id');
                $table->string('phone', 20);
                $table->string('storeEmail');
                $table->integer('storePhone');
                $table->string('country');
                $table->string('state');
                $table->string('city');
                $table->string('address');
                $table->string('pincode');
                $table->string('employeeCode')->nullable();
                $table->dateTime('created_at')->useCurrent();
                $table->dateTime('updated_at')->nullable();
                $table->dateTime('deleted_at')->nullable();
            });
        }

        if (! Schema::hasTable('whishlist')) {
            Schema::create('whishlist', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('listing_id');
                $table->integer('user_id');
                $table->dateTime('created_at')->useCurrent();
                $table->dateTime('updated_at')->useCurrent();
                $table->dateTime('deleted_at')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('whishlist');
        Schema::dropIfExists('sellers');
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('listings');
        Schema::dropIfExists('listingphotos');
        Schema::dropIfExists('listingdatas');
        Schema::dropIfExists('listingadditions');
        Schema::dropIfExists('listing_sizechart');
        Schema::dropIfExists('carts');
        Schema::dropIfExists('brands');
        Schema::dropIfExists('brandrequests');
        Schema::dropIfExists('attributes');
    }
}
