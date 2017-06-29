<?php
/**
 * Created by PhpStorm.
 * User: vanhs
 * Date: 3/3/17
 * Time: 1:51 PM
 */

namespace App\Library\ServiceFee;

abstract class AbstractService {
    protected $unit_price = 0;
    protected $fixed_fee = 0;
    protected $min_fee = 0;
    protected $total_fee = 0;
    protected $total_amount = 0;
    protected $apply_time = null;
    protected $model = null;
    protected $service = null;
    protected $service_code = null;

    abstract function calculatorFee();
    abstract function getServiceCode();
}