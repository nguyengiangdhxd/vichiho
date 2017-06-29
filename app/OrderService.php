<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderService extends Model
{
    protected $table = 'order_service';

    const STATUS_ACTIVE = 'ACTIVE';
    const STATUS_INACTIVE = 'INACTIVE';

    public static function addService($order_id, $service_code){
        $order_service = new self();
        $order_service->order_id = $order_id;
        $order_service->service_code = $service_code;
        return $order_service->save();
    }

    public static function removeService($order_id, $service_code){
        return self::where([
            'order_id' => $order_id,
            'service_code' => $service_code
        ])->delete();
    }

    public static function findByOrderIds($order_ids = []){
        if(!count($order_ids)) return [];
        return OrderService::whereIn('order_id', $order_ids)->get();
    }

    public static function findByOrderId($order_id = null){
        if(!$order_id) return [];
        return OrderService::where('order_id', $order_id)->get();
    }
}
