<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServiceWoodCratingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_wood_crating', function (Blueprint $table) {
            $table->increments('id');
            $table->double('min_volume', 10, 2);
            $table->double('max_volume', 10, 2);
            $table->double('fee', 20, 2);
            $table->double('private_fee', 20, 2);
            $table->tinyInteger('is_max_volume')->default(0);
            $table->float('volume_added')->default(0);
            $table->double('fee_added', 20, 2);
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
        Schema::dropIfExists('service_wood_crating');
    }
}
