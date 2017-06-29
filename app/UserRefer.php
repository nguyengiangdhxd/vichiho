<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserRefer extends Model
{
    protected $table = 'user_refer';

    public static function addNew(User $user_refer, User $user){
        if(!self::isExists($user_refer->id, $user->id)){
            $row = new self();
            $row->user_refer_id = $user_refer->id;
            $row->user_id = $user->id;
            return $row->save();
        }
        return true;
    }

    public static function isExists($user_refer_id, $user_id){
        $row = self::where([
            'user_id' => $user_id,
            'user_refer_id' => $user_refer_id
        ])->first();
        if($row instanceof self){
            return true;
        }
        return false;
    }
}
