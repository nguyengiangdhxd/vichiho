<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToOrderFreightBill extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_freight_bill', function (Blueprint $table) {
            $table->tinyInteger('is_deleted')->nullable()->default(0)->after('freight_bill');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_freight_bill', function (Blueprint $table) {
            //
        });
    }
}
