<?php

namespace App\Http\Controllers;

use App\Order;
use App\OrderFreightBill;
use App\Package;
use App\Permission;
use App\User;
use App\Util;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class PackageController extends Controller
{
    protected $action_error = [];

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @author vanhs
     * @desc Cac hanh dong tren trang tao kien
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function action(Request $request)
    {
        try{
            DB::beginTransaction();

            $action = '__' . $request->get('action');
            $package_id = $request->get('package_id');
            $package = null;
            if($package_id){
                $package = Package::find($package_id);
                if(!$package instanceof Package){
                    return response()->json(['success' => false, 'message' => 'Không tìm thấy package #' . $package_id]);
                }
                if($package->isEndingStatus()){
                    return response()->json(['success' => false, 'message' => sprintf('Kiện hàng hiện đã ở trạng thái cuối (%s), không thể thay đổi thông tin!', Package::getStatusTitle($package->status))]);
                }
            }

            if (!method_exists($this, $action)) {
                return response()->json(['success' => false, 'message' => 'Not support action!']);
            }

            $result = $this->$action($request, $package);
            if(!$result){
                return response()->json( ['success' => false, 'message' => implode('<br>', $this->action_error)] );
            }

            DB::commit();

            $html = null;
            if($request->get('response')){
                $view = View::make($request->get('response'), $this->__getInitData('layouts/app_blank'));
                $html = $view->render();
            }

            return response()->json([
                'success' => true,
                'message' => 'success',
                'html' => $html,
                'result' => $result,
            ]);

        }catch(\Exception $e){
            DB::rollback();
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra, vui lòng thử lại' . $e->getMessage()]);
        }

    }

    /**
     * @author vanhs
     * @desc Cap nhat thong tin kien hang
     * @param Request $request
     * @param $package
     * @return array|bool
     */
    private function __update_package(Request $request, $package){
        $height_package = doubleval($request->get('height_package'));
        $width_package = doubleval($request->get('width_package'));
        $length_package = doubleval($request->get('length_package'));

        $package->note = $request->get('note');
        $package->height_package = $height_package;
        $package->width_package = $width_package;
        $package->length_package = $length_package;
        $package->weight = $request->get('weight');
        $package->weight_type = $request->get('weight_type_' . $package->id);

        $converted_weight = ($length_package * $width_package * $height_package) / 6000;
        $package->converted_weight = $converted_weight;

        if($package->save()){
            return [
                'package' => $package,
            ];
        }
        return false;
    }

    /**
     * @author vanhs
     * @desc Ham xoa kien, chi ap dung voi nhung kien khong map voi don hang
     * @param Request $request
     * @param $package
     * @return bool
     */
    private function __delete_package(Request $request, $package){
        if(!$package->order_id){
            $package->is_deleted = 1;
            return $package->save();
        }
        return true;
    }

    /**
     * @author vanhs
     * @desc Tao kien hang khi quet ma van don
     * - Neu kien khop voi don hang thi tien hanh cap nhat cac thong tin
     *      + ma kien
     *      + id don hang
     *      + id khach hang
     *      + id dia chi dat hang
     * @param $order
     * @param null $barcode
     * @return bool|null
     */
    private function __create_package_item($order, $barcode = null){
        if(empty($barcode)) return null;

        $package = new Package();
        if($order instanceof Order){
            $code = Package::genPackageCode($order);
            $package->code = $code;
            $package->order_id = $order->id;
            $package->buyer_id = $order->user_id;
            $package->user_address_id = $order->user_address_id;
        }else{

        }

        $package->logistic_package_barcode = Package::generateBarcode();
        $package->status = Package::STATUS_INIT;
        $package->freight_bill = $barcode;
        $package->created_by = Auth::user()->id;
        return $package->save();
    }

    /**
     * @author vanhs
     * @desc Tao kien bang ma van don
     * @param Request $request
     * @param $package
     * @return bool
     */
    private function __create_package(Request $request, $package){
        $barcode = $request->get('barcode');

        if(empty($barcode)){
            $this->action_error[] = 'Mã quét không để trống!';
        }

        $can_create = Permission::isAllow(Permission::PERMISSION_PACKAGE_ADD);
        if(!$can_create){
            $this->action_error[] = 'Bạn không có quyền!';
        }

        if(count($this->action_error)){
            return false;
        }

        $orders_freight_bill = OrderFreightBill::select('order_id')->where([
            'freight_bill' => $barcode,
            'is_deleted' => 0,
        ])->get();

        $orders_freight_bill = count($orders_freight_bill) > 0 ? $orders_freight_bill : [ null ];

        foreach($orders_freight_bill as $order_freight_bill){
            $order = null;
            if($order_freight_bill instanceof OrderFreightBill){
                $order = Order::find($order_freight_bill->order_id);
            }
            $this->__create_package_item($order, $barcode);
        }
        return true;
    }

    private function __getInitData($layout = null, $barcode = null){
        $packages = null;
        if($barcode){
            $packages = Package::where([
                'freight_bill' => $barcode,
                'status' => Package::STATUS_INIT,
                'is_deleted' => 0,
            ])->orderBy('id', 'desc')
                ->get();

            if(count($packages)){
                foreach($packages as $key => $package){
                    if(!$package instanceof Package){
                        continue;
                    }

                    $packages[$key]->order = null;
                    $packages[$key]->customer = null;
                    $packages[$key]->customer_address = null;

                    $order = Order::find($package->order_id);
                    $customer = User::find($package->buyer_id);

                    if($order instanceof Order){
                        $packages[$key]->order = $order;
                        $packages[$key]->customer_address = $order->getCustomerReceiveAddress();
                    }
                    if($customer instanceof User){
                        $packages[$key]->customer = $customer;
                    }

                }
            }
        }

        return [
            'page_title' => 'Tạo kiện',
            'layout' => $layout,
            'packages' => $packages,
            'barcode' => $barcode,
        ];
    }

    public function index(Request $request)
    {
        $action = $request->get('action');
        $logistic_package_barcode = $request->get('logistic_package_barcode');
        if($action){
            switch ($action){
                case 'print':
                    $generatorSVG = new \Picqer\Barcode\BarcodeGeneratorSVG();

                    $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();

                    $logistic_package_barcode_array = explode(',', $logistic_package_barcode);
                    foreach($logistic_package_barcode_array as $logistic_package_barcode_item){

                        $package = Package::retrieveByCode($logistic_package_barcode_item);
                        if($package instanceof Package){
                            $svg = $generatorSVG->getBarcode(
                                $logistic_package_barcode_item,
                                $generator::TYPE_CODE_128,
                                1,
                                30
                            );
                            $view = View::make('logistic_package_barcode_print', [
                                'package' => $package,
                                'svg' => $svg,
                                'img_base_64' => base64_encode(
                                    $generator->getBarcode($logistic_package_barcode_item, $generator::TYPE_CODE_128))
                            ]);
                            $html = $view->render();
                            echo $html;
                        }else{
                            echo '<br>Khong tim thay kien #' . $logistic_package_barcode_item;
                        }

                    }
                    break;
            }
            exit;
        }

        $data = $this->__getInitData('layouts.app', $request->get('barcode'));

        return view('package_add', $data);
    }

    private function __getDetailData(Request $request, $layout = null){
        $package_code = $request->route('code');
        $package = Package::retrieveByCode($package_code);
        if(!$package instanceof Package){
            return redirect('403');
        }

        $order = null;
        $customer = null;
        $customer_address = null;
        $packages_order = null;

        $order = Order::find($package->order_id);
        $customer = User::find($package->buyer_id);

        if($order instanceof Order){
            $package->order = $order;
            $package->customer_address = $order->getCustomerReceiveAddress();

            $packages_order = $package->getPackagesWithOrder();
        }
        if($customer instanceof User){
            $package->customer = $customer;
        }

        return [
            'page_title' => 'Thông tin kiện hàng ' . $package_code,
            'package_code' => $package_code,
            'layout' => $layout,
            'package' => $package,
            'packages_order' => $packages_order,
        ];
    }

    public function detail(Request $request)
    {
        $data = $this->__getDetailData($request, 'layouts.app');
        return view('package_detail', $data);
    }

    private function __getListData($layout = null){
        $per_page = 50;

        $packages = Package::select('*')
            ->where([
                'is_deleted' => 0,
            ])
            ->orderBy('id', 'desc');
        $total_packages = $packages->count();
        $packages = $packages->paginate($per_page);

        if($packages){
            foreach($packages as $key => $package){
                $packages[$key]->order = Order::find($package->order_id);
                $packages[$key]->customer = User::find($package->buyer_id);
            }
        }

        $can_create_package = Permission::isAllow(Permission::PERMISSION_PACKAGE_ADD);

        return [
            'page_title' => 'Danh sách kiện hàng',
            'packages' => $packages,
            'total_packages' => $total_packages,
            'can_create_package' => $can_create_package,
            'layout' => $layout
        ];
    }

    public function indexs()
    {
        $can_view = Permission::isAllow(Permission::PERMISSION_PACKAGE_LIST_VIEW);
        if(!$can_view){
            return redirect('403');
        }

        $data = $this->__getListData('layouts.app');

        return view('package_list', $data);
    }
}
