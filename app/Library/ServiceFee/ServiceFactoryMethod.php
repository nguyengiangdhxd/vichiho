<?php
/**
 * Created by PhpStorm.
 * User: vanhs
 * Date: 3/3/17
 * Time: 1:48 PM
 */

namespace App\Library\ServiceFee;

class ServiceFactoryMethod extends AbstractFactoryMethod {
    private $context = "Service";

    function makeService($data) {
        $object = NULL;

        $service_code = $data['service_code'];

        if($service_code):
            $array_temp = [];
            $words = explode('_', $service_code);
            foreach($words as $word):
                $array_temp[] = ucfirst(strtolower($word));
            endforeach;

            $className = "App\\Library\\ServiceFee\\" . implode('', $array_temp);
            if(class_exists($className)):
                return $object = new $className($data);
            endif;


        endif;

        return $object;
    }
}