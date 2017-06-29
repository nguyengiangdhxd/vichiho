<?php

namespace App\Http\Controllers\Customer;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function indexs(){
        return view('customer/notification', [
            'page_title' => 'Thông báo'
        ]);
    }
}
