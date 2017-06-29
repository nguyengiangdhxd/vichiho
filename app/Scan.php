<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Scan extends Model
{
    protected $table = 'scan';

    const ACTION_IN = 'IN';
    const ACTION_OUT = 'OUT';

    public static $action_list = [
        self::ACTION_IN => 'Nhập',
        self::ACTION_OUT => 'Xuất',
    ];


    public static function findByUser($created_by){
        return self::where(['created_by' => $created_by])->orderBy('created_at', 'desc')->get();
    }

    public static function getActionName($code){
        return empty(self::$action_list[$code]) ? '' : self::$action_list[$code];
    }
}
