<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCartItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cart_id')->nullable()->default(0);
            $table->integer('user_id')->nullable()->default(0);
            $table->string('title_origin', 100)->nullable();
            $table->string('title_translated', 100)->nullable();
            $table->double('price_origin', 20, 2)->nullable()->default(0);
            $table->double('price_promotion', 20, 2)->nullable()->default(0);
            $table->string('price_table', 100)->nullable();
            $table->string('data_value', 100)->nullable();
            $table->string('property', 50)->nullable();
            $table->string('property_translated', 50)->nullable();
            $table->string('property_md5', 50)->nullable();
            $table->mediumText('image_origin', 100)->nullable();
            $table->mediumText('image_model', 100)->nullable();
            $table->string('seller_id', 50)->nullable();
            $table->string('shop_id', 50)->nullable();
            $table->string('shop_name', 50)->nullable();
            $table->string('wangwang', 50)->nullable();
            $table->integer('quantity')->nullable()->default(0);
            $table->integer('require_min')->nullable()->default(0);
            $table->integer('stock')->nullable()->default(0);
            $table->string('location_sale', 100)->nullable();
            $table->string('site', 10)->nullable();
            $table->string('item_id', 30)->nullable();
            $table->string('link_origin', 100)->nullable();
            $table->string('outer_id', 50)->nullable();
            $table->double('weight', 20, 2)->nullable()->default(0);
            $table->integer('error')->nullable();
            $table->integer('step')->nullable()->default(0);
            $table->string('tool', 50)->nullable();
            $table->string('version', 50)->nullable();
            $table->integer('is_translate')->nullable();
            $table->string('comment', 100)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cart_items');
    }
}
