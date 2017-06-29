<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Location extends Model
{
    protected $table = 'locations';

    const TYPE_DISTRICT = 'DISTRICT';
    const TYPE_STATE = 'STATE';

    public static function getAllProvinces(){
        return self::addSelect('*')
            ->where(['type' => self::TYPE_STATE, 'status' => 0])
            ->orderBy('logistic_code', 'asc')
            ->get();
    }

    public static function getAllDistricts(){
        return self::addSelect('*')
            ->where(['type' => self::TYPE_DISTRICT, 'status' => 0])
            ->orderBy('logistic_code', 'asc')
            ->get();
    }

    public static function checkProvinceContainDistrict($province_id, $district_id){
        $total = self::select('id')
            ->where(['parent_id' => $province_id, 'id' => $district_id])
            ->count();
        if($total):
            return true;
        endif;

        return false;
    }
}

