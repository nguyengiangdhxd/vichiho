<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToOrder4 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order', function (Blueprint $table) {
            $table->timestamp('seller_delivery_at')->nullable()->before('created_at');
            $table->timestamp('received_from_seller_at')->nullable()->before('created_at');
            $table->timestamp('transporting_at')->nullable()->before('created_at');
            $table->timestamp('customer_delivery_at')->nullable()->before('created_at');
            $table->timestamp('delivering_at')->nullable()->before('created_at');
            $table->timestamp('received_at')->nullable()->before('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order', function (Blueprint $table) {
            //
        });
    }
}
