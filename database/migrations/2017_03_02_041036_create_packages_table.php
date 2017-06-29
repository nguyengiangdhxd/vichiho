<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code', 50)->nullable();
            $table->string('status', 50)->nullable();
            $table->string('freight_bill', 50)->nullable();
            $table->integer('order_id')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('buyer_id')->nullable();
            $table->integer('user_address_id')->nullable();
            $table->decimal('weight', 20, 2);
            $table->string('current_warehouse', 100)->nullable();
            $table->string('warehouse_status', 20)->nullable();
            $table->timestamp('warehouse_status_in_at')->nullable();
            $table->timestamp('warehouse_status_out_at')->nullable();
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
        Schema::dropIfExists('packages');
    }
}
