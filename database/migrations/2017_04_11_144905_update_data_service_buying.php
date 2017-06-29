<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateDataServiceBuying extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('service_buying', function (Blueprint $table) {
            //
        });

//        DB::statement(" truncate table `service_buying` ");
//
//        DB::statement(" INSERT INTO `service_buying` (`fee_percent`, `min_fee`, `begin`, `end`, `status`, `actived_time`, `deadline_time`, `created_at`, `updated_at`)
//VALUES
//	(1.0, 0.00, 0.00, 1000000000000000000.00, 'ACTIVE', '2016-04-11 14:46:45', '2030-03-17 00:00:00', '2017-03-17 00:00:00', NULL);
// ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('service_buying', function (Blueprint $table) {
            //
        });
    }
}
