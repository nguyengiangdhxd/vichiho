<?php

namespace App\Http\Controllers;

use App\SystemConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HomePageController extends Controller
{
    public function __construct()
    {

    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function homePage(){
        $data_return = [
            'enable_popup' => false,
            'title_popup' => null,
            'content_popup' => null,
        ];
        foreach($data_return as $k => $v){
            $config = SystemConfig::getConfigValueByKey('home_page_' . $k);
            $data_return[$k] = $config ? $config : $v;
        }
        return view('home/index', $data_return);
    }
}
