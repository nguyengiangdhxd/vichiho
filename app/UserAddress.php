<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Location;

class UserAddress extends Model
{
    protected $table = 'user_address';

    public static function getUserAddressMax(){
        $user_address_max = SystemConfig::getConfigValueByKey('user_address_max');
        if($user_address_max){
            return $user_address_max;
        }
        return 5;
    }

    public static function findByUserId($user_id){
        return self::select('*')
            ->where([
                'user_id' => $user_id,
                'is_delete' => 0
            ])
            ->orderBy('is_default', 'desc')->get();
    }

    public static function checkMaxUserAddress($user_id){
        $total = self::select('id')
            ->where([
                'user_id' => $user_id,
                'is_delete' => 0,
            ])
            ->count();
        if($total < self::getUserAddressMax()):
            return true;
        endif;

        return false;
    }

    public static function addNewUserAddress($send_data){
        try{
            DB::beginTransaction();

            $user_address_id = $send_data['user_address_id'];
            unset($send_data['user_address_id']);
            unset($send_data['_token']);

            if(!$user_address_id):
                self::where([
                        'user_id' => $send_data['user_id'],
                    ])
                    ->update([
                        'is_default' => 0
                    ]);

                $send_data['is_default'] = 1;
                self::insert($send_data);


            else:
                self::where(['id' => $user_address_id, 'user_id' => $send_data['user_id']])
                    ->update($send_data);
            endif;

            DB::commit();
            return true;
        }catch(\Exception $e){
            DB::rollback();
            return false;
        }
    }

    public static function setDefaultUserAddress($id, $user_id){

        try{
            DB::beginTransaction();

            self::where([
                    'user_id' => $user_id
                ])
                ->update([
                    'is_default' => 0
                ]);

            self::where(['id' => $id, 'user_id' => $user_id])
                ->update([
                    'is_default' => 1
                ]);

            DB::commit();
            return true;
        }catch(\Exception $e){
            DB::rollback();
            return false;
        }

    }
}
