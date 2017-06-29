<?php

namespace App\Http\Controllers\Support;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TaxonomyController extends Controller
{
    public function indexs(){
        return view('support/taxonomy', [
            'page_title' => 'Danh mục bài viết',
        ]);
    }
}
