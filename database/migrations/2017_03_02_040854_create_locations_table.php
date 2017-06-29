<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('label', 150);
            $table->char('key_code', 5);
            $table->enum('type', ['COUNTRY','STATE','DISTRICT','VILLAGE']);
            $table->integer('parent_id')->default(0);
            $table->tinyInteger('status');
            $table->string('CODE', 20);
            $table->tinyInteger('can_delete')->default(1);
            $table->integer('ordering');
            $table->char('logistic_code', 5);
            $table->string('warehouse', 10);
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
        Schema::dropIfExists('locations');
    }
}
