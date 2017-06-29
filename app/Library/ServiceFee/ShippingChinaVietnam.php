<?php

namespace App\Library\ServiceFee;
use App\Service;
use App\ServiceShipping;
use App\WareHouse;

class ShippingChinaVietnam extends AbstractService {

    function __construct($data) {
        $this->service = new Service();

        $this->model = new ServiceShipping();
        foreach($data as $key => $val):
            $this->{$key} = $val;
        endforeach;

        #region Lay ra cong thuc tinh phi
        $result = $this->model->getCurrentPolicy($this->weight, $this->destination_warehouse, $this->apply_time);
        if($result):
            $this->unit_price = $result->weight_fee;
        endif;
        #endregion

        #region Lay ra phi co dinh doi voi dich vu van chuyen TQ - VN
        $this->fixed_fee = $this->service->getFixedFeeWithServiceCode($this->getServiceCode());
        #endregion
    }

    function getServiceCode()
    {
        return $this->service_code;
    }

    /**
     * @desc Phi van chuyen quoc te TQ - VN duoc tinh bang
     * - phi co dinh (cau hinh trong bang services)
     * - phi van chuyen = can nang * don gia (trong bang service_shipping)
     * @return mixed
     */
    function calculatorFee()
    {
        return $this->fixed_fee + ($this->unit_price * $this->weight);
    }
}