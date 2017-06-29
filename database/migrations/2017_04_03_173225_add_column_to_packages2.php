<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToPackages2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->float('width_package')->nullable()->after('weight')->comment('chieu rong');
            $table->float('height_package')->nullable()->after('weight')->comment('chieu cao');
            $table->float('length_package')->nullable()->after('weight')->comment('chieu dai');
            $table->float('converted_weight')->nullable()->after('weight')->comment('can nang quy doi');
            $table->timestamp('received_from_seller_at')->before('created_at')->nullable();
            $table->timestamp('transporting_at')->before('created_at')->nullable();
            $table->timestamp('waiting_delivery_at')->before('created_at')->nullable();
            $table->timestamp('delivering_at')->before('created_at')->nullable();
            $table->timestamp('received_at')->before('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('packages', function (Blueprint $table) {
            //
        });
    }
}
