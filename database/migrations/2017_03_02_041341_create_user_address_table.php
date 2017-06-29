<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserAddressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_address', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('district_id');
            $table->integer('province_id');
            $table->string('detail', 255);
            $table->string('note', 255);
            $table->string('reciver_name', 100);
            $table->string('reciver_phone', 100);
            $table->integer('is_default');
            $table->integer('is_order');
            $table->tinyInteger('is_delete');
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
        Schema::dropIfExists('user_address');
    }
}
