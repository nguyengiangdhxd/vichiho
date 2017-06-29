<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->unique();
            $table->char('code', 20)->unique()->default('');
            $table->double('account_balance')->default(0);
            $table->enum('section', ['CUSTOMER', 'CRANE'])->default('CUSTOMER');
            $table->enum('status', ['ACTIVE', 'INACTIVE', 'LOCK', 'DELETE'])->default('ACTIVE');
            $table->string('avatar')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
}
