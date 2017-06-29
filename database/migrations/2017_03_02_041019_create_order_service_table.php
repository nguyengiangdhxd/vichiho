<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderServiceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_service', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_id');
            $table->string('service_code', 100);
//            $table->integer('service_id');
//            $table->double('unit_price', 20, 2);
//            $table->double('quantity', 20, 2);
//            $table->double('fixed_fee', 20, 2);
//            $table->double('money');
//            $table->double('discounted_money');
//            $table->double('estimated_money');
//            $table->float('before_payment_money');
//            $table->string('status', 50);
//            $table->tinyInteger('is_charged_fee')->default(0);
//            $table->tinyInteger('is_calculate_auto_fee')->default(1);
//            $table->string('calculator_fee', 20);
//            $table->string('free_fee', 20);
//            $table->string('was_changed_fee', 20);
//            $table->float('was_payment_weight');
//            $table->enum('buying_service_type', ['BUYING_NOW','BUYING_NOT_NOW'])->default('BUYING_NOT_NOW');
//            $table->timestamp('buying_service_time');
//            $table->timestamp('charged_time');
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
        Schema::dropIfExists('order_service');
    }
}
