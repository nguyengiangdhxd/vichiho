<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserMobilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_mobiles', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('mobile', 50);
            $table->string('coming_by', 50)->default('SMS_GATEWAY');
            $table->integer('verify_times')->default(1);
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
        Schema::dropIfExists('user_mobiles');
    }
}
