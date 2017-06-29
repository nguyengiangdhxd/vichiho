<?php

namespace App\Http\Controllers\Customer;
use App\Comment;
use App\Exchange;
use App\Http\Controllers\Controller;
use App\Location;
use App\Order;
use App\OrderItem;
use App\OrderService;
use App\Package;
use App\Service;
use App\User;
use App\UserAddress;
use App\UserOriginalSite;
use App\UserTransaction;
use App\Util;
use App\WareHouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;

class OrderController extends Controller
{

    protected $action_error = [];

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @author vanhs
     * @desc Danh sach don hang
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function orders(Request $request){
        $params = Input::all();
        $exchange_rage = Exchange::getExchange();

        $per_page = 50;
        $orders = Order::select('*');
        $orders = $orders->orderBy('id', 'desc');

        if(!empty($params['order_code'])){
            $orders = $orders->where('code', $params['order_code']);
        }

        if(!empty($params['customer_code_email'])){
            $user_ids = User::where(function($query) use ($params){
                $query->where('code', '=', $params['customer_code_email'])
                    ->orWhere('email', '=', $params['customer_code_email']);
            })->pluck('id');
            $orders = $orders->whereIn('user_id', $user_ids);
        }

        $orders = $orders->where('user_id', Auth::user()->id);

        if(!empty($params['status'])){
            $orders = $orders->whereIn('status', explode(',', $params['status']));
        }
        $total_orders = $orders->count();
        $orders = $orders->paginate($per_page);


        $status_list = [];
        foreach(Order::$statusTitle as $key => $val){
            $selected = false;
            if(!empty($params['status'])){
                $selected = in_array($key, explode(',', $params['status']));
            }
            $status_list[] = [
                'key' => $key,
                'val' => $val,
                'selected' => $selected
            ];
        }

        return view('customer/orders', [
            'page_title' => 'Danh sách đơn hàng',
            'status_list' => $status_list,
            'exchange_rage' => $exchange_rage,
            'orders' => $orders,
            'params' => $params,
            'total_orders' => $total_orders,
        ]);
    }

    /**
     * @author vanhs
     * @desc Chi tiet don hang
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function order(Request $request){
        $order_id = $request->route('id');

        $order = Order::findOneByIdOrCode($order_id);
        $current_user = User::find(Auth::user()->id);

        if(!$order || !$order instanceof Order):
            return redirect('404');
        endif;

        $customer = User::find($order->user_id);

        if($customer->id != $current_user->id):
            return redirect('403');
        endif;

        return view('customer/order_detail', $this->__getOrderInitData($order, $customer, 'layouts.app'));
    }

    private function __getOrderInitData(Order $order, User $customer, $layout){
        $user_address = UserAddress::find($order->user_address_id);
        if($user_address && $user_address instanceof UserAddress){
            $district = Location::find($user_address->district_id);
            if($district && $district instanceof Location){
                $user_address->district_label = $district->label;
            }
            $province = Location::find($user_address->province_id);
            if($province && $province instanceof Location){
                $user_address->province_label = $province->label;
            }
        }

        $order_item_comments_data = [];
        $order_item_comments = Order::findByOrderItemComments($order->id);
        if($order_item_comments){
            foreach($order_item_comments as $order_item_comment){
                $order_item_comment->user = User::find($order_item_comment->user_id);
                $order_item_comments_data[$order_item_comment->object_id][] = $order_item_comment;
            }
        }

        $permission = [
            'can_change_order_bought' => $order->status == Order::STATUS_DEPOSITED,
            'can_change_order_cancel' => $order->isBeforeStatus(Order::STATUS_BOUGHT),
            'can_change_order_service' => $order->isBeforeStatus(Order::STATUS_BOUGHT),
            'can_change_order_received_from_seller' => $order->status == Order::STATUS_SELLER_DELIVERY,
            'can_change_order_item_quantity' => $order->isBeforeStatus(Order::STATUS_BOUGHT),
            'can_change_order_item_price' => $order->isBeforeStatus(Order::STATUS_BOUGHT),
            'can_change_order_account_purchase_origin' => $order->isBeforeStatus(Order::STATUS_BOUGHT),
            'can_change_order_domestic_shipping_fee' => $order->isBeforeStatus(Order::STATUS_BOUGHT),
            'can_change_order_deposit_percent' => $order->isBeforeStatus(Order::STATUS_BOUGHT),
        ];

        $packages = $order->package()->where([
            'is_deleted' => 0,
        ])->get();

        $fee = $order->fee($customer, $packages);

        $order_fee = [];
        foreach(Order::$fee_field_order_detail as $key => $label){
            $value = $key;
            if(isset($fee[$key])){
                $value = Util::formatNumber($fee[$key]);
            }
            $order_fee[] = [
                'label' => $label,
                'value' => $value,
            ];
        }

        $order_service = [];
        $o_services = $order->service;
        foreach($o_services as $o_service){
            if(!$o_service || !$o_service instanceof OrderService){
                continue;
            }
            $order_service[] = $o_service->service_code;
        }

        $services = [];
        $services_list = Service::getAllService();
        if($services_list){
            foreach($services_list as $service){
                if(!$service || !$service instanceof Service){
                    continue;
                }
                $services[] = [
                    'code' => $service->code,
                    'name' => Service::getServiceName($service->code),
                    'is_default' => Service::checkIsDefault($service->code),
                    'checked' => in_array($service->code, $order_service) ? true : false
                ];
            }
        }

        return [
            'order_id' => $order->id,
            'packages' => $packages,
            'freight_bill' => $order->freight_bill()->where([ 'is_deleted' => 0 ])->get(),
            'original_bill' => $order->original_bill()->where([ 'is_deleted' => 0 ])->get(),
            'warehouse_distribution' => WareHouse::findByType(WareHouse::TYPE_DISTRIBUTION),
            'warehouse_receive' => WareHouse::findByType(WareHouse::TYPE_RECEIVE),
            'user_address' => $user_address,
            'order' => $order,
            'services' => $services,
            'order_item_comments' => $order_item_comments_data,
            'user_origin_site' => UserOriginalSite::all(),
            'order_items' => $order->item,
            'order_fee' => $order_fee,
            'customer' => $customer,
            'transactions' => Order::findByTransactions($order->id),
            'page_title' => 'Chi tiết đơn hàng',
            'permission' => $permission,
            'layout' => $layout,
        ];
    }

    /**
     * @author vanhs
     * @desc Xu ly toan bo hanh dong trang trang chi tiet don hang
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function action(Request $request){
        try{
            DB::beginTransaction();

            $order_id = $request->route('id');
            $order = Order::find($order_id);
            $current_user = User::find(Auth::user()->id);
            $action = '__' . $request->get('action');

            if(!$order){
                return response()->json(['success' => false, 'message' => 'Order not found!']);
            }

            $customer = User::find($order->user_id);

            if(!$current_user || !$current_user instanceof User){
                return response()->json(['success' => false, 'message' => 'Current user not found!']);
            }

            if(!$customer || !$customer instanceof User){
                return response()->json(['success' => false, 'message' => 'Customer not found!']);
            }

            if($order->user_id <> $customer->id){
                return response()->json(['success' => false, 'message' => 'Action reject!']);
            }

            if (!method_exists($this, $action)) {
                return response()->json(['success' => false, 'message' => 'Not support action!']);
            }

            $result = $this->$action($request, $order, $current_user);
            if(!$result){
                return response()->json( ['success' => false, 'message' => implode('<br>', $this->action_error)] );
            }

            DB::commit();

            $view = View::make($request->get('response'), $this->__getOrderInitData($order, $customer, 'layouts/app_blank'));
            $html = $view->render();

            return response()->json([
                'success' => true,
                'message' => 'success',
                'html' => $html
            ]);

        }catch(\Exception $e){
            DB::rollback();
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra, vui lòng thử lại']);
        }
    }

//    private function __customer_delivery(Request $request, Order $order, User $user){
//        if($order->status != Order::STATUS_WAITING_DELIVERY){
//            $this->action_error[] = 'Trạng thái không hợp lệ!';
//        }
//
//        if(count($this->action_error)){
//            return false;
//        }
//
//        $order->changeStatus(Order::STATUS_CUSTOMER_DELIVERY);
//
//        $total_package_weight_payment = 0;
//        $packages_payment = $order->package()->where([ 'status' => '' ])->get();
//        if($packages_payment){
//            foreach($packages_payment as $package_payment){
//                if(!$package_payment instanceof Package){
//                    continue;
//                }
//                $total_package_weight_payment += (float)$package_payment->weight;
//            }
//        }
//
//        $customer_payment_order = UserTransaction::getPaymentOrder($user, $order);
//        $total_order_payment = 0;
//
//        $total_amount_vnd = $order->amountWithItems(true);
//        $shipping_china_vietnam = $order->getShippingChinaVietnam($total_package_weight_payment);
//        $total_order_payment += $total_amount_vnd;
//        $total_order_payment += $order->getBuyingFee($total_amount_vnd);
//        if($order->existService(Service::TYPE_CHECKING)){
//            $total_order_payment += $order->getCheckingFee();
//        }
//        if($order->existService(Service::TYPE_WOOD_CRATING)){
//            $total_order_payment += $order->getWoodCrating($shipping_china_vietnam, 10, 10);
//        }
//
//        Comment::createComment($user, $order, "Chuyển trạng thái đơn hàng sang yêu cầu giao", Comment::TYPE_INTERNAL, Comment::TYPE_CONTEXT_ACTIVITY);
//        Comment::createComment($user, $order, "Chuyển trạng thái đơn hàng sang yêu cầu giao", Comment::TYPE_EXTERNAL, Comment::TYPE_CONTEXT_ACTIVITY);
//
//        if($customer_payment_order <> $total_order_payment){
//            $temp = 'truy thu';
//            $user_transaction_amount = 0 - abs($customer_payment_order - $total_order_payment);
//            if($customer_payment_order > $total_order_payment){
//                $user_transaction_amount = abs($customer_payment_order - $total_order_payment);
//                $temp = 'trả lại';
//            }
//
//            $message = sprintf("Hệ thống tiến hành %s số tiền %s",
//                $temp,
//                Util::formatNumber(abs($customer_payment_order - $total_order_payment)));
//
//            Comment::createComment($user, $order, $message, Comment::TYPE_INTERNAL, Comment::TYPE_CONTEXT_ACTIVITY);
//            Comment::createComment($user, $order, $message, Comment::TYPE_EXTERNAL, Comment::TYPE_CONTEXT_ACTIVITY);
//
//            UserTransaction::createTransaction(
//                UserTransaction::TRANSACTION_TYPE_DEPOSIT_ADJUSTMENT,
//                $message,
//                $user,
//                $user,
//                $order,
//                $user_transaction_amount
//            );
//        }
//
//        return true;
//    }


    /**
     * @author vanhs
     * @desc Huy don hang
     * @param Request $request
     * @param Order $order
     * @param User $user
     * @return bool
     */
    private function __cancel_order(Request $request, Order $order, User $user){
        if($order->isAfterStatus(Order::STATUS_BOUGHT, true)){
            $this->action_error[] = 'Không thể hủy đơn hàng. Liên hệ với NhatMinh247 để được hỗ trợ';
        }

        if(count($this->action_error)){
            return false;
        }

        $order->changeStatus(Order::STATUS_CANCELLED);

        $customer = User::find($order->user_id);

        $deposit_amount = UserTransaction::getDepositOrder($customer, $order);
        if($deposit_amount < 0){
            UserTransaction::createTransaction(
                UserTransaction::TRANSACTION_TYPE_REFUND,
                sprintf('Trả lại tiền đặt cọc đơn hàng %s', $order->code),
                $user,
                $customer,
                $order,
                abs($deposit_amount)
            );
        }

        Comment::createComment($user, $order, "Hủy đơn hàng.", Comment::TYPE_EXTERNAL, Comment::TYPE_CONTEXT_ACTIVITY);

        return true;
    }

    /**
     * @author vanhs
     * @desc Commment san pham
     * @param Request $request
     * @param Order $order
     * @param User $user
     * @return bool
     */
    private function __order_item_comment(Request $request, Order $order, User $user){
        $item_id = $request->get('item_id');
        $order_item = OrderItem::find($item_id);
        return Comment::createComment(
            $user,
            $order_item,
            $request->get('order_item_comment_message'),
            Comment::TYPE_NONE,
            Comment::TYPE_CONTEXT_CHAT,
            $order
        );
    }

    private function __choose_service(Request $request, Order $order, User $user){
        $service = $request->get('service');
        if(empty($service)){
            $this->action_error[] = 'Chưa chọn dịch vụ!';
        }

        $can_execute = $order->isBeforeStatus(Order::STATUS_BOUGHT);
        if(!$can_execute):
            $this->action_error[] = sprintf('Không thể chọn/bỏ dịch vụ ở trạng thái này %s!', Order::getStatusTitle($order->status));
        endif;

        if(count($this->action_error)){
            return false;
        }

        if(in_array($service, Service::getServiceDefault())){
            return true;
        }

        $message = null;
        $exist_service = $order->existService($service);
        if($request->get('checkbox') == 'check'){
            if(!$exist_service){
                $order_service = new OrderService();
                $order_service->order_id = $order->id;
                $order_service->service_code = $service;
                $order_service->save();

                $message = sprintf("Chọn dịch vụ %s", Service::getServiceName($service));
            }
        }else{
            if($exist_service){
                OrderService::where([
                    'order_id' => $order->id,
                    'service_code' => $service
                ])->delete();

                $message = sprintf("Bỏ chọn dịch vụ %s", Service::getServiceName($service));
            }
        }

        Comment::createComment($user, $order, $message, Comment::TYPE_EXTERNAL, Comment::TYPE_CONTEXT_ACTIVITY);

        return true;
    }

}
