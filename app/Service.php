<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Service extends Model
{

    protected $table = 'services';

    const STATUS_ACTIVE = 'ACTIVE';
    const STATUS_IN_ACTIVE = 'IN_ACTIVE';

    const TYPE_BUYING = 'BUYING';
    const TYPE_CHECKING = 'CHECKING';
    const TYPE_SHIPPING_CHINA_VIETNAM = 'SHIPPING_CHINA_VIETNAM';
    const TYPE_WOOD_CRATING = 'WOOD_CRATING';

    public static $serviceNaming = [
        self::TYPE_BUYING => 'Mua Hàng',
        self::TYPE_CHECKING => 'Kiểm Hàng',
        self::TYPE_SHIPPING_CHINA_VIETNAM => 'VC Quốc Tế',
        self::TYPE_WOOD_CRATING => 'Đóng Gỗ',
    ];

    public static $serviceIcon = [
        self::TYPE_BUYING => 'fa-shopping-cart',
        self::TYPE_CHECKING => 'fa-chevron-down',
        self::TYPE_SHIPPING_CHINA_VIETNAM => 'fa-truck',
        self::TYPE_WOOD_CRATING => 'fa-cube',
    ];

    /**
     * @author vanhs
     * @desc Cac dich vu ma khach hang duoc phep lua chon tren gio hang
     * @var array
     */
    public static $service_customer_choose = [
        self::TYPE_CHECKING => 'Kiểm Hàng',
        self::TYPE_WOOD_CRATING => 'Đóng Gỗ',
    ];

    public static $service_default = [
        self::TYPE_BUYING,
        self::TYPE_SHIPPING_CHINA_VIETNAM,
    ];

    public static function checkIsDefault($code){
        if(in_array($code, self::$service_default)){
            return true;
        }
        return false;
    }

    public static function getServiceDefault(){
        return self::$service_default;
    }

    public static function getServiceIcon($code) {
        return (isset(self::$serviceIcon[$code]))? self::$serviceIcon[$code] : '';
    }

    public static function getServiceName($code) {
        return (isset(self::$serviceNaming[$code]))? self::$serviceNaming[$code] : 'Khác';
    }

    public static function getAllService(){
        return self::where(['status' => self::STATUS_ACTIVE])->get();
    }

    public function findOneByCode($code){
        if($code) return null;

        $result = self::where([
            'status' => self::STATUS_ACTIVE,
            'code' => $code
        ])->first();
        if($result) return $result;

        return null;
    }

    /**
     * @desc Lay phi co dinh doi voi tung dich vu
     * @param $code
     * @return mixed
     */
    public function getFixedFeeWithServiceCode($code){
        $fee = 0;
        $result = self::where([
            'status' => self::STATUS_ACTIVE,
            'code' => $code
        ])->first();

        if($result):
            $fee = $result->fixed_fee;
        endif;

        return $fee;
    }
}
