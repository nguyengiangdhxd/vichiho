<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServiceDiscountTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_discount', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('level_id');
            $table->string('service', 255);
            $table->float('value')->default(0);
            $table->enum('type', ['PERCENT','FIX','FIX_UNIT_PRICE']);
            $table->tinyInteger('active')->default(0);
            $table->timestamp('actived_time');
            $table->timestamp('deadline_time');
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
        Schema::dropIfExists('service_discount');
    }
}
