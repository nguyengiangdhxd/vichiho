<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServiceShippingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_shipping', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 50);
            $table->string('type', 20);
            $table->string('sub_type', 100);
            $table->string('vehicle', 100);
            $table->decimal('weight_from', 4, 1);
            $table->decimal('weight_to', 4, 1);
            $table->double('weight_fee', 20, 2);
            $table->string('properties', 50);
            $table->text('description');
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
        Schema::dropIfExists('service_shipping');
    }
}
