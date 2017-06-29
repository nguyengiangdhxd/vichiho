<?php

namespace App\Library\ServiceFee;

/**
 * Created by PhpStorm.
 * User: vanhs
 * Date: 3/3/17
 * Time: 1:47 PM
 */

abstract class AbstractFactoryMethod {
    abstract function makeService($param);
}