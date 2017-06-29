<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ServiceChecking extends Model
{
    protected $table = 'service_checking';

    const STATUS_ACTIVE = 'ACTIVE';
    const STATUS_DISABLED = 'DISABLED';

    public function getCurrentPolicy($total_quantity, $apply_time)
    {
        return $this->newQuery()
            ->select('*')
            ->where([
                ['status', '=', self::STATUS_ACTIVE],
                ['begin', '<', $total_quantity],
                ['end', '>=', $total_quantity],
                ['actived_time', '<=', $apply_time],
                ['deadline_time', '>', $apply_time]
            ])
            ->orderBy('actived_time', 'desc')
            ->first();
    }

}
