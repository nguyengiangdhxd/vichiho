<?php

namespace App\Http\Controllers\Customer;

use App\ProductFavorite;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ProductFavoriteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function indexs(){
        $per_page = 50;
        $product_favorite = ProductFavorite::where([
            'user_id' => Auth::user()->id,
        ]);
        $product_favorite = $product_favorite->orderBy('id', 'desc');

        $total_product_favorite = $product_favorite->count();

        $product_favorite = $product_favorite->paginate($per_page);

        return view('customer/product_favorite', [
            'page_title' => 'Sản phẩm đã lưu',
            'product_favorite' => $product_favorite,
            'total_product_favorite' => $total_product_favorite,
        ]);
    }
}
