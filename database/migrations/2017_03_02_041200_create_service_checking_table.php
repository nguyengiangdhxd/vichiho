<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServiceCheckingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_checking', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('begin');
            $table->integer('end');
            $table->double('normal_item', 20, 2);
            $table->double('accessory_item', 20, 2);
            $table->enum('status', ['ACTIVE','DISABLED'])->default('ACTIVE');
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
        Schema::dropIfExists('service_checking');
    }
}
