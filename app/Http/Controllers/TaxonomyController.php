<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TaxonomyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function createTaxonomy()
    {
        return view('taxonomy_add', [
            'page_title' => 'Tạo nhóm bài viết',
        ]);
    }

    public function indexs(){
        return view('taxonomy_list', [
            'page_title' => 'Quản lý nhóm bài viết',
        ]);
    }
}
