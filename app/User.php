<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\UserMobile;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'code'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $table = 'users';

    const STATUS_ACTIVE = 'ACTIVE';
    const STATUS_INACTIVE = 'INACTIVE';

    const SECTION_CRANE = 'CRANE';
    const SECTION_CUSTOMER = 'CUSTOMER';

    const SITE_TAOBAO = 'taobao';
    const SITE_TMALL = 'tmall';
    const SITE_1688 = '1688';

    public static $status_list = [
        self::STATUS_ACTIVE => 'Kích hoạt',
        self::STATUS_INACTIVE => 'Ngừng kích hoạt'
    ];

    public static $site_list = [
        self::SITE_TAOBAO => 'Taobao',
        self::SITE_TMALL => 'Tmall',
        self::SITE_1688 => '1688'
    ];

    public static $god = [
//        'hosivan90@gmail.com',
//        'nguyenhoanggiangdhxd@gmail.com',
        'nguyengiangdhxd@gmail.com',
        'cherry@gmail.com',
    ];

    public static $section_list = [
        self::SECTION_CRANE => 'Quản trị viên',
        self::SECTION_CUSTOMER => 'Khách hàng',
    ];

    /**
     * @author vanhs
     * @desc Lay tong so user dang ky moi theo ngay
     * @param $day
     * @return int
     */
    public static function getTotalRegisterByDay($day){//Y-m-d
        $total = self::select('id')->where([
            [ 'created_at', '>=', $day . ' 00:00:00' ],
            [ 'created_at', '<=', $day . ' 23:59:59' ],
        ])->count();
        return $total;
    }

    /**
     * @author vanhs
     * @desc Kiem tra xem user hien tai co dang hoat dong hay khong?
     * @return bool
     */
    public function isDisabled(){
        if($this->status != self::STATUS_ACTIVE){
            return true;
        }
        return false;
    }

    /**
     * @author vanhs
     * @desc Kiem tra xem user hien tai co phai la quan tri hay khong?
     * @return bool
     */
    public function isCrane(){
        if($this->section == self::SECTION_CRANE){
            return true;
        }
        return false;
    }

    public static function getMaxMobile(){
        $user_mobile_max = SystemConfig::getConfigValueByKey('user_mobile_max');
        if($user_mobile_max){
            return $user_mobile_max;
        }
        return 3;
    }

    public static function retrieveByCode($code){
        if(empty($code)) return null;

        return self::where(['code' => $code])->first();
    }

    protected static function statusList(){
        return self::$status_list;
    }

    protected static function sectionList(){
        return self::$section_list;
    }

    public static function getStatusName($status = null){
        return empty(self::$status_list[$status]) ? '' : self::$status_list[$status];
    }

    public static function getSectionName($section = null){
        return empty(self::$section_list[$section]) ? '' : self::$section_list[$section];
    }

    public static function genCustomerCode(){
        $vowel = array('A', 'E', 'I', 'O', 'U');
        $consonants = array('B', 'C', 'D', 'G', 'H', 'K', 'M', 'P', 'R', 'S', 'T', 'V', 'X');
        $times = 0;
        do {
            $char_part = $consonants[array_rand($consonants)] . $vowel[array_rand($vowel)];
            $number_part = Util::getLuckyNumber(4);
            $code = "{$char_part}{$number_part}";

            $check = DB::table('users')->where('code', $code)->value('code');
            $times++;
        } while ($check && $times <= 8);

        return $code;
    }

    public function updateAccountBalance($amount, $user_id){
        try{
            DB::beginTransaction();

            if($amount > 0){
                $raw = DB::raw("account_balance+{$amount}");
            }else{
                $amount = abs($amount);
                $raw = DB::raw("account_balance-{$amount}");
            }

            self::where([
                'id' => $user_id,
            ])->update([
                'account_balance' => $raw
            ]);

            DB::commit();
            return true;
        }catch(\Exception $e){
            DB::rollback();
            return false;
        }
    }

    public function findByMobiles(){
        return UserMobile::where(['user_id' => $this->id])->get();
    }

    public function addMobile($mobile){
        return UserMobile::insert([
            'user_id' => $this->id,
            'mobile' => $mobile,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function deleteMobile($mobile){
        return UserMobile::where([
            'user_id' => $this->id,
            'mobile' => $mobile
        ])->delete();
    }

    public function checkExistsMobile($mobile){
        return UserMobile::where([
            'mobile' => $mobile
        ])->first();
    }

    public function checkMaxMobile(){
        return UserMobile::where([
            'user_id' => $this->id
        ])->count() >= self::getMaxMobile();
    }


    public function address(){
        return $this->hasMany('App\UserAddress', 'order_id');
    }

    public function mobile(){
        return $this->hasMany('App\UserMobile', 'user_id');
    }

    public function role(){
        return $this->hasMany('App\UserRole', 'user_id');
    }

    /**
     * @author vanhs
     * @desc Ham lay ra 1 so dien thoai cua khach hang (so lien lac moi nhat duoc tao)
     * @return mixed|string
     */
    public function getMobile(){
        $row = UserMobile::orderBy('id', 'desc')->first();
        if($row instanceof UserMobile){
            return $row->mobile;
        }
        return '';
    }

    /**
     * @author vanhs
     * @desc Hàm lấy ra kho đích của đơn hàng khách
     * 1. Lấy theo cấu hình bằng tay, nếu có
     * 2. Lấy tự động theo location mapping
     * @return null
     */
    public function destination_warehouse(){
        $user_warehouse_manually = UserWarehouse::where([
            'user_id' => $this->id,
        ])->first();
        if($user_warehouse_manually):
            return $user_warehouse_manually->warehouse_code;
        endif;

        $user_address = UserAddress::where([
            'is_default' => 1,
            'user_id' => $this->id
        ])->first();

        if(!$user_address):
            return null;
        endif;

        $district_id = $user_address->district_id;
        $province_id = $user_address->province_id;

        if($district_id):
            $location = Location::where([
                'id' => $district_id,
                'type' => Location::TYPE_DISTRICT
            ])->first();
            if($location && $location->warehouse):
                return $location->warehouse;
            endif;
        endif;

        if($province_id):
            $location = Location::where([
                'id' => $province_id,
                'type' => Location::TYPE_STATE
            ])->first();
            if($location && $location->warehouse):
                return $location->warehouse;
            endif;
        endif;

        return null;
    }
}
