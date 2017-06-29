<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserTransactionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_transaction', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->enum('state', ['PENDING','COMPLETED','CANCELED','REJECTED'])->default('PENDING');
            $table->string('transaction_code', 50);
            $table->enum('transaction_type', ['DEPOSIT','WITHDRAWAL','REFUND','ORDER_DEPOSIT','ORDER_PAYMENT','ADJUSTMENT','CHARGE_FEE','GIFT','PAYMENT','PROMOTION','REFUND_COMPLAINT','DEPOSIT_ADJUSTMENT']);
            $table->double('amount', 20, 2);
            $table->double('ending_balance', 20, 2);
            $table->integer('created_by');
            $table->string('object_id', 255);
            $table->string('object_type', 20);
            $table->string('transaction_detail', 255);
            $table->string('transaction_note', 255);
            $table->char('checksum', 64);
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
        Schema::dropIfExists('user_transaction');
    }
}
