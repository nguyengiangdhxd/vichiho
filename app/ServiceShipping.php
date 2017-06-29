<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ServiceShipping extends Model
{
    protected $table = 'service_shipping';

    const SUB_TYPE_01 = 'HN';
    const SUB_TYPE_02 = 'SG';

    const STATUS_ACTIVE = 'ACTIVE';
    const STATUS_DISABLED = 'DISABLED';

    const TYPE_CHINA_VIETNAM = 'CHINA_VIETNAM';
    const TYPE_EXPRESS_CHINA_VIETNAM = 'EXPRESS_CN_VN';

    public static $sub_type_list = [
        self::SUB_TYPE_01 => '01',
        self::SUB_TYPE_02 => '02',
    ];

    public static function getSubTypeWarehouse($warehouse){
        return empty(self::$sub_type_list[$warehouse]) ? '' : self::$sub_type_list[$warehouse];
    }

    public function getCurrentPolicy($weight, $destination_warehouse, $apply_time){
        if($weight == 0):
            $weight = 0.1;
        endif;

        $warehouse = new WareHouse();
        $warehouse_alias = $warehouse->getAliasByCode($destination_warehouse);
        $sub_type = self::getSubTypeWarehouse($warehouse_alias);

        return $this->newQuery()
            ->select('*')
            ->where([
                ['status', '=', self::STATUS_ACTIVE],
                ['type', '=', self::TYPE_CHINA_VIETNAM],
                ['sub_type', '=', $sub_type],
                ['weight_from', '<', $weight],
                ['weight_to', '>=', $weight],
                ['actived_time', '<=', $apply_time],
                ['deadline_time', '>', $apply_time]
            ])
            ->orderBy('actived_time', 'desc')
            ->first();
    }
}
