<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Exchange extends Model
{
    protected $table = 'exchange_rate';

    const STATUS_ACTIVE = 'ACTIVE';
    const STATUS_DISABLED = 'DISABLED';

    /**
     * @author vanhs
     * @desc Ham lay ti gia hien tai
     * @param null $apply_time
     * @return int
     */
    public static function getExchange($apply_time = null){

        $value = 0;
        if(!$apply_time):
            $apply_time = date('Y-m-d H:i:s');
        endif;

        $row = self::where([
            [ 'actived_time', '<=', $apply_time ],
            [ 'deadline_time', '>', $apply_time ],
            [ 'status', '=', self::STATUS_ACTIVE ]
        ])->first();

        if($row):
            $value = $row->value;
        endif;

        return $value;
    }


}
