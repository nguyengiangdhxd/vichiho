<?php

namespace App\Library\ServiceFee;
use App\Order;
use App\OrderItem;
use App\Service;
use App\ServiceChecking;

class Checking extends AbstractService {

    public function __construct($data)
    {
        $this->service = new Service();

        $this->model = new ServiceChecking();
        foreach($data as $key => $val):
            $this->{$key} = $val;
        endforeach;

        #region Lay ra phi co dinh doi voi dich vu kiem hang
        $this->fixed_fee = $this->service->getFixedFeeWithServiceCode($this->getServiceCode());
        #endregion
    }

    function getServiceCode()
    {
        return $this->service_code;
    }

    /**
     * @desc Phi kiem hang = phi co dinh kiem hang + (SL SP thuong * don gia) + (SL SP phu kien * don gia)
     * @return int
     */
    function calculatorFee()
    {
        $price_normal = 0;
        $price_assess = 0;

        $result1 = $this->model->getCurrentPolicy($this->total_quantity_items_normal, $this->apply_time);
        if($result1):
            $price_normal = $result1->normal_item;
        endif;

        $result2 = $this->model->getCurrentPolicy($this->total_quantity_items_assess, $this->apply_time);
        if($result2):
            $price_assess = $result2->accessory_item;
        endif;

        $this->total_fee = ($this->total_quantity_items_normal * $price_normal)
            + ($this->total_quantity_items_assess * $price_assess)
            + $this->fixed_fee;

        return $this->total_fee;
    }
}