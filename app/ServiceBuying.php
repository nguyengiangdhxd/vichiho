<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ServiceBuying extends Model
{
    protected $table = 'service_buying';

    const STATUS_ACTIVE = 'ACTIVE';
    const STATUS_DISABLED = 'DISABLED';

    public function getCurrentPolicy($total_amount, $apply_time)
    {
        return $this->newQuery()
            ->select('*')
            ->where([
                ['status', '=', self::STATUS_ACTIVE],
                ['begin', '<', $total_amount],
                ['end', '>=', $total_amount],
                ['actived_time', '<=', $apply_time],
                ['deadline_time', '>', $apply_time]
            ])
            ->orderBy('actived_time', 'desc')
            ->first();
    }
}
