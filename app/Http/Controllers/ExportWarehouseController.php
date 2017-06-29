<?php
/**
 * Created by PhpStorm.
 * User: goerge
 * Date: 19/02/2017
 * Time: 16:15
 */

namespace App\Http\Controllers;


use App\Packages;
use App\WareHouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExportWarehouseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

    }

    /**
     * load lần đầu vào trang
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(){
         $warehouse = WareHouse::all();
        return view('export_warehouse',[
            'page_title' => 'Xuất Nhập kho',
            'warehouse' => $warehouse
        ]);
    }

    /**
     * sau này sẽ có thể xuất bao ship_ho cho kh
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function actionWarehouse(Request $request){
        // get request tu
        $input = $request->all();
        Log::info('User failed to login.',['input' => $input]);
        if(!$input['barcode']){
            $response = array(
                'status' => 'error',
                'msg' => 'Setting created successfully',
            );

            return  response()->json($response);
        }

        $isPackage = Packages::isPackage($input['barcode']);
        if($isPackage){
            $package_barcode = $input['barcode'];
            // kiểm tra xem dữ liệu nhận được
            //$packge_date = Packages::find($package_barcode);
            $package = DB::table('packages')->where('logistic_package_barcode', $package_barcode)->first();
             if($package instanceof Packages){
                 // n
             }


            // kiểm tra xem có là kiện ko , neeus ko laf kieen thì check là bao khách hàng
            // xuất toàn bộ kiện trong bao
        }elseif($isPackage){
            Log::info('User failed to login.', ['id']);
           // sẽ xử lý bao ở đ
            
        }
        return response()->json($input);
    }

}