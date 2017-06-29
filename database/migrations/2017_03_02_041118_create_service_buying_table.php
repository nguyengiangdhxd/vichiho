<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServiceBuyingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_buying', function (Blueprint $table) {
            $table->increments('id');
            $table->decimal('fee_percent', 4, 1);
            $table->double('min_fee', 20, 2);
            $table->double('begin', 20, 2);
            $table->double('end', 20, 2);
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
        Schema::dropIfExists('service_buying');
    }
}
