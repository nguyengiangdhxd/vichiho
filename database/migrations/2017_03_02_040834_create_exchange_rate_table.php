<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExchangeRateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exchange_rate', function (Blueprint $table) {
            $table->increments('id');
            $table->double('value', 20, 2);
            $table->enum('status', ['ACTIVE','DISABLED']);
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
        Schema::dropIfExists('exchange_rate');
    }
}
