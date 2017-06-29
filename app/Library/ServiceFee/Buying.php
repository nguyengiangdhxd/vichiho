<?php

namespace App\Library\ServiceFee;
use App\Service;
use App\ServiceBuying;

class Buying extends AbstractService {

    function __construct($data) {
        $this->service = new Service();

        $this->model = new ServiceBuying();
        foreach($data as $key => $val):
            $this->{$key} = $val;
        endforeach;

        #region Lay ra cong thuc tinh phi
        $result = $this->model->getCurrentPolicy($this->total_amount, $this->apply_time);
        if($result):
            $this->unit_price = $result->fee_percent;
            $this->min_fee = $result->min_fee;
        endif;
        #endregion

        #region Lay ra phi co dinh doi voi dich vu mua hang
        $this->fixed_fee = $this->service->getFixedFeeWithServiceCode($this->getServiceCode());
        #endregion
    }

    function getServiceCode()
    {
        return $this->service_code;
    }

    /**
     * @desc Phi mua hang = phi mua hang tinh theo cong thuc phi (dua tren tong gia tri don) + phi mua hang co dinh
     * @return mixed
     */
    function calculatorFee()
    {
        $this->total_fee = $this->total_amount * ($this->unit_price / 100);
        if($this->total_fee < $this->min_fee):
            $this->total_fee = $this->min_fee;
        endif;

        return $this->fixed_fee + $this->total_fee;
    }
}