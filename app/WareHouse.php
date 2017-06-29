<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WareHouse extends Model
{
    protected $table = 'warehouse';

    const TYPE_DISTRIBUTION = 'DISTRIBUTION';
    const TYPE_RECEIVE = 'RECEIVE';

    const WAREHOUSE_ALIAS_HN = 'HN';
    const WAREHOUSE_ALIAS_SG = 'SG';

    public static $type_warehouse = [
        self::TYPE_DISTRIBUTION => 'Kho phân phối',
        self::TYPE_RECEIVE => 'Kho nhận hàng',
    ];

    public static function getTypeNameWarehouse($type){
        return empty(self::$type_warehouse[$type]) ? '' : self::$type_warehouse[$type];
    }

    /**
     * @desc Lấy tên alias của kho dựa theo mã kho | Vd: VNHN, K-HN có alias là HN; VNSG, K-SG có alias là SG...
     * @param $code
     * @return mixed|string
     */
    public function getAliasByCode($code) {
        $alias = "";
        $result = $this->newQuery()->where([
            'code' => $code
        ])->first();

        if($result):
            $alias = $result->alias;
        endif;

        return $alias;
    }

    public static function findByType($type){
        return self::where([
            'type' => $type
        ])
        ->orderBy('alias', 'asc')
        ->orderBy('ordering', 'asc')
        ->get();
    }

    public static function retrieveByCode($code = null){
        if(!$code) return null;

        return self::where([ 'code' => $code ])->first();
    }

    public static function getAllWarehouse(){
        return self::orderBy('type', 'desc')->get();
    }
}
