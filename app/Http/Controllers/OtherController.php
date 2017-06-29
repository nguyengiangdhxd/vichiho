<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OtherController extends Controller
{

    public function __construct()
    {

    }

    public function renderPageNotFound()
    {
        return view('not_found', [
            'page_title' => 'Page 404'
        ]);
    }

    public function renderPageNotPermission()
    {
        return view('not_permission', [
            'page_title' => 'Page 403'
        ]);
    }

    public function renderExampleVue(){
        return view('example_vue', [
            'page_title' => 'Example Vue'
        ]);
    }
}
