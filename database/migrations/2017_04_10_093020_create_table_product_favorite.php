<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableProductFavorite extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_favorite', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('user_id')->nullable();
            $table->string('site', 50)->nullable();
            $table->mediumText('link')->nullable();
            $table->mediumText('avatar')->nullable();
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
        Schema::dropIfExists('product_favorite');
    }
}
