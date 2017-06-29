<?php

namespace App\Http\Controllers;

use App\Library\ServiceFee\AbstractService;
use App\Library\ServiceFee\Buying;
use App\Library\ServiceFee\ServiceFactoryMethod;
use App\Service;
use App\OrderFreightBill;
use App\Order;
use App\SystemConfig;
use App\User;
use App\Role;
use App\UserRole;
use App\Permission;
use App\Util;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user_id = Auth::user()->id;

        /*

        $factoryMethodInstance = new ServiceFactoryMethod();

        $order_id = 19;

        //============phi mua hang===========
        $service = $factoryMethodInstance->makeService([
            'service_code' => Service::TYPE_BUYING,
            'total_amount' => 6000,
            'apply_time' => '2017-09-01 00:00:00'
        ]);
        var_dump('phi mua hang');
        var_dump($service->calculatorFee());

        //============phi kiem hang===========
        $order = Order::find($order_id);
        $total_quantity_items_normal = $order->getItemNormalQuantity();
        $total_quantity_items_assess = $order->getItemAssessQuantity();

        $service = $factoryMethodInstance->makeService([
            'service_code' => Service::TYPE_CHECKING,
            'total_quantity_items_normal' => $total_quantity_items_normal,
            'total_quantity_items_assess' => $total_quantity_items_assess,
            'apply_time' => '2017-09-01 00:00:00'
        ]);
        var_dump('phi kiem hang');
        var_dump($service->calculatorFee());

        //============phi van chuyen TQ-VN===========
        $order = Order::find($order_id);
        $destination_warehouse = $order->destination_warehouse;
        $destination_warehouse = 'BT-SG';

        //-- Can nang truyen vao phai chuyen sang kg
        $service = $factoryMethodInstance->makeService([
            'service_code' => Service::TYPE_SHIPPING_CHINA_VIETNAM,
            'weight' => 10,
            'destination_warehouse' => $destination_warehouse,
            'apply_time' => '2017-09-01 00:00:00'
        ]);
        var_dump('phi van chuyen TQ - VN');
        var_dump($service->calculatorFee());

        //============phi dong go===========
        $service = $factoryMethodInstance->makeService([
            'service_code' => Service::TYPE_WOOD_CRATING,
            'fee_shipping_china_vietnam' => 50000,//phi van chuyen quoc te
            'weight_manual' => 10,//can nang tinh
            'weight_equivalent' => 50,//can nang quy doi
            'calculator_by' => 'MANUAL',//MANUAL hoac EQUIVALENT
            'apply_time' => '2017-09-01 00:00:00'
        ]);
        var_dump('phi dong go');
        var_dump($service->calculatorFee());

        */

        // Store a piece of data in the session...

//        $order_freight_bill = Order::find(3)->freight_bill;
//        $order_original_bill = Order::find(3)->original_bill;
//
//        $role = User::find(2)->role;
//

//        $user_permission = Cache::get("user_permission_{$user_id}");
//        var_dump($user_permission);

//        die('hello');

//        $orders = Order::all();
//        foreach($orders as $order){
//            $order->save();
//        }

//        $order = Order::find(83);
//        $order->domestic_shipping_fee = 9;
//        $order->save();

        $current_user = User::find($user_id);

        $total_order_deposit_today = 0;
        $total_customer_register_today = 0;

        if($current_user->section == User::SECTION_CRANE){
            $total_order_deposit_today = Order::getTotalDepositByDay(date('Y-m-d'));
            $total_customer_register_today = User::getTotalRegisterByDay(date('Y-m-d'));
        }

        return view('home', [
            'page_title' => 'Trang chủ',
            'current_user' => $current_user,
            'total_order_deposit_today' => $total_order_deposit_today,
            'total_customer_register_today' => $total_customer_register_today,
        ]);
    }
}
