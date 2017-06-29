<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->nullable()->default(0);
            $table->string('seller_id', 255)->nullable();
            $table->string('wangwang', 255)->nullable();
            $table->string('location_sale', 255)->nullable();
            $table->string('shop_id', 50)->nullable();
            $table->string('shop_name', 50)->nullable();
            $table->string('shop_link', 100)->nullable();
            $table->string('avatar', 100)->nullable();
            $table->string('site', 50)->nullable();
            $table->string('services', 100)->nullable();
            $table->string('comment', 255)->nullable();
            $table->string('comment_private', 255)->nullable();
            $table->timestamp('last_insert_item_at')->nullable()->before('created_at');
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
        Schema::dropIfExists('carts');
    }
}
