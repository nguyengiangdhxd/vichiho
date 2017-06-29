<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_item', function (Blueprint $table) {
            $table->increments('id');
            $table->string('item_id', 50)->nullable();//id sp trung quoc
            $table->integer('order_id');
            $table->integer('user_id');
            $table->string('title', 255)->nullable();
            $table->string('title_translated', 255)->nullable();
            $table->string('link', 255)->nullable();
            $table->string('image', 255)->nullable();
            $table->string('property', 255)->nullable();
            $table->string('property_translated', 255)->nullable();
            $table->string('location_sale', 100)->nullable();
            $table->double('price', 20, 2);//NDT
            $table->double('price_promotion', 20, 2);//NDT
            $table->text('price_table')->nullable();
            $table->integer('order_quantity');
            $table->integer('checking_quantity');
            $table->integer('receiver_quantity');
            $table->integer('step');
            $table->integer('require_min');
            $table->integer('stock');
            $table->char('site', 50)->nullable();//site goc [taobao, tmall, 1688]
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
        Schema::dropIfExists('order_item');
    }
}
