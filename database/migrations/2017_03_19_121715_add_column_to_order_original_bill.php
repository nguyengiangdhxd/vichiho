<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToOrderOriginalBill extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_original_bill', function (Blueprint $table) {
            $table->tinyInteger('is_deleted')->nullable()->default(0)->after('original_bill');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_original_bill', function (Blueprint $table) {
            //
        });
    }
}
