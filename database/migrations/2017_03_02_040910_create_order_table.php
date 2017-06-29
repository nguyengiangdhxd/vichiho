<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order', function (Blueprint $table) {
            $table->increments('id');
            $table->char('code', 50)->nullable();//ma don hang
            $table->string('avatar', 255)->nullable();
            $table->string('status', 50)->nullable();//trang thai don
            $table->char('site', 50)->nullable();//site goc [taobao, tmall, 1688]
            $table->integer('total_order_quantity')->nullable()->default(0);//tong so luong dat
            $table->integer('total_checking_quantity')->nullable()->default(0);//tong so luong kiem
            $table->integer('total_receiver_quantity')->nullable()->default(0);//tong so luong nhan
            $table->decimal('exchange_rate', 20, 2)->nullable();//ti gia - VND
            $table->integer('user_id')->nullable()->default(0);//id khac hang
            $table->integer('paid_staff_id')->nullable()->default(0);//id nhan vien mua hang
            $table->integer('teller_id')->nullable()->default(0);//id nhan vien thanh toan
            $table->integer('user_address_id')->nullable()->default(0);//thong tin dia chi nhan hang khi dat coc don
            $table->double('amount', 20, 2)->nullable()->default(0);//tong gia tri don hang - NDT
            $table->char('destination_warehouse', 50)->nullable();//kho dich cua don hang
            $table->decimal('weight', 6, 2);//tong can nang don hang
            $table->double('deposit_percent', 20, 2);//ti le dat coc don hang
            $table->double('deposit_amount', 20, 2);//tien dat coc - VND
            $table->double('domestic_shipping_fee', 20, 2);//phi van chuyen noi dia TQ - NDT
            $table->string('account_purchase_origin', 50);//acc mua hang site goc
            $table->timestamp('deposited_at')->nullable();//thoi gian dat coc
            $table->timestamp('cancelled_at')->nullable();//thoi gian huy don cua khach
            $table->timestamp('out_of_stock_at')->nullable();//thoi gian an nut het hang
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
        Schema::dropIfExists('order');
    }
}
