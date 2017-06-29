<?php

namespace App\Library\ServiceFee;
use App\Service;
use App\ServiceWoodCrating;

class WoodCrating extends AbstractService {

    const TYPE_CALCULATOR_MANUAL = 'MANUAL';
    const TYPE_CALCULATOR_EQUIVALENT = 'EQUIVALENT';

    function __construct($data) {
        $this->service = new Service();

        $this->model = new ServiceWoodCrating();
        foreach($data as $key => $val):
            $this->{$key} = $val;
        endforeach;

        #region Lay ra cong thuc tinh phi
//        $result = $this->model->getCurrentPolicy($this->weight, $this->destination_warehouse, $this->apply_time);
//        if($result):
//            $this->unit_price = $result->weight_fee;
//        endif;
        #endregion

        #region Lay ra phi co dinh doi voi dich vu dong go
        $this->fixed_fee = $this->service->getFixedFeeWithServiceCode($this->getServiceCode());
        #endregion
    }

    function calculatorFee()
    {
        return 0;
    }

    function getServiceCode()
    {
        return $this->service_code;
    }
}