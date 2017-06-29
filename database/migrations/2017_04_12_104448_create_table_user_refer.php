<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUserRefer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_refer', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('user_id')->nullable()->unique();
            $table->tinyInteger('user_refer_id')->nullable();
            $table->dateTime('start_enjoyment_at')->nullable();
            $table->dateTime('end_enjoyment_at')->nullable();
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
        Schema::dropIfExists('user_refer');
    }
}
