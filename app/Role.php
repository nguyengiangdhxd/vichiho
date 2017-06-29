<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';

    const STATE_ACTIVE = 'ACTIVE';
    const STATE_INACTIVE = 'INACTIVE';

    public static  $stateList = [
        self::STATE_ACTIVE => 'Kích hoạt ',
        self::STATE_INACTIVE => 'Ngừng kích hoạt '
    ];


    public static function getStateName($name){
        if(!empty(self::$stateList[$name])):
            return self::$stateList[$name];
        endif;
        return '';
    }
}
