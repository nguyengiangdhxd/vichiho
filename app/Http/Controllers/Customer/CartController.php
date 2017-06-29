<?php

namespace App\Http\Controllers\Customer;

use App\Permission;
use App\UserTransaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\CartItem;
use App\Exchange;
use App\Order;
use App\UserAddress;
use Illuminate\Support\Facades\Response;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Service;
use App\Cart;
use JavaScript;
use App\Location;
use Illuminate\Support\Facades\View;

class CartController extends Controller
{
    public $cart = null;

    protected $action_error = [];

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @author vanhs
     * @desc Ham kiem tra cac dieu kien dau vao truoc khi tien hanh dat coc
     * @param User $user
     * @param $destination_warehouse
     * @param $shop_id
     * @param $address_id
     * @param $password
     * @param $deposit_amount
     * @return null|string
     */
    private function __validateBeforeDepositOrder(User $user, $destination_warehouse, $shop_id,
                                                  $address_id, $password,
                                                  $deposit_amount){
        if(!$user):
            return 'User not found!';
        endif;

        if($user->isCrane()):
            return 'Tài khoản quản trị không thể đặt cọc.';
        endif;

        if($user->isDisabled()):
            return 'Tài khoản đã ngừng hoạt động.';
        endif;

        if(!count($shop_id)):
            return 'Không tồn tại shop.';
        endif;

        $check_exists_address = UserAddress::select('id')->where([
            'user_id' => $user->id,
            'is_delete' => 0
        ])->count();

        if(!$check_exists_address):
            return 'Hiện chưa có địa chỉ nhận hàng.';
        endif;

        $check_exists_address_default = UserAddress::select('id')->where([
            'user_id' => $user->id,
            'is_default' => 1,
            'is_delete' => 0
        ])->count();

        if(!$check_exists_address_default):
            return 'Chưa có địa chỉ nhận hàng mặc định.';
        endif;

        $user_address = UserAddress::find($address_id);
        if(!$user_address):
            return 'Địa chỉ nhận hàng không hợp lệ.';
        endif;

        if(!$password || !Hash::check($password, $user->password)):
            return 'Mật khẩu không chính xác.';
        endif;

        if(!($user->account_balance >= $deposit_amount)):
            return 'Tài khoản không đủ tiền để thực hiện đặt cọc.';
        endif;

        if(empty($destination_warehouse)):
            return 'Không tìm thấy kho đích của khách.';
        endif;

        return null;
    }

    /**
     * @author vanhs
     * @desc Xu ly hanh dong dat coc don hang
     * @param Request $request
     * @return mixed
     */
    public function depositOrder(Request $request){
        try{
            $user_id = Auth::user()->id;
            $password = $request->get('password');
            $shop_id = $request->get('shop_id');
            $address_id = $request->get('address_id');
            $exchange_rate = Exchange::getExchange();
            $user = User::find($user_id);
            $destination_warehouse = $user->destination_warehouse();

            #region lay ra tong so tien hang theo danh sach shop
            $total_shop_amount = 0;
            $cart_items = CartItem::select('*')
                ->where([
                    'user_id' => $user_id
                ])
                ->whereIn('shop_id', $shop_id)
                ->get();

            if($cart_items):
                foreach($cart_items as $cart_item):
                    $price = $cart_item->getPriceCalculator();
                    $quantity = $cart_item->quantity;
                    $total_shop_amount += ($price * $quantity) * $exchange_rate;
                endforeach;
            endif;
            #endregion

            $deposit_percent = Cart::getDepositPercent();
            $deposit_amount = Cart::getDepositAmount($deposit_percent, $total_shop_amount);

            $message = $this->__validateBeforeDepositOrder($user, $destination_warehouse, $shop_id, $address_id,
                $password, $deposit_amount);

            if($message):
                return Response::json(['success' => false, 'message' => $message]);
            endif;

            $result = Cart::depositOrder($user, $destination_warehouse, $shop_id,
                $address_id, $exchange_rate,
                $deposit_percent, $deposit_amount);

            if($result){
                $redirect_url = url('dat-coc-thanh-cong?orders=' . implode(',', $result));

                return Response::json(['success' => true,
                    'redirect_url' => $redirect_url,
                    'message' => 'Đặt cọc đơn thành công. Xin cám ơn!']);
            }

            return Response::json(['success' => false,
                'message' => 'Đặt cọc không thành công. Vui lòng thử lại!']);

        }catch (\Exception $e){
            return Response::json(['success' => false,
                'message' => 'Đặt cọc không thành công. Vui lòng thử lại!']);

        }
    }

    /**
     * @author vanhs
     * @desc Hien thi trang dat coc don thanh cong
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function depositSuccess(Request $request){
        $ids = $request->get('orders');
        $orders = [];
        if($ids){
            $list = explode(',', $ids);
            if(count($list)){
                $orders = Order::whereIn('id', $list)->get();
            }
        }

        $data = [
            'page_title' => 'Đặt cọc thành công',
            'orders' => $orders
        ];

        return view('customer/deposit_success', $data);
    }

    /**
     * @author vanhs
     * @desc Hien thi thong tin trang dat coc don hang
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showDeposit(Request $request){
        $user_id = Auth::user()->id;
        $exchange_rate = Exchange::getExchange();

        $shops = [];
        $shop_id = $request->get('shop_id');
        $shop_id_list = $shop_id ? explode(',', $shop_id) : [];
        if(count($shop_id_list)):
            $shops = Cart::where([
                'user_id' => $user_id,
            ])->whereIn('shop_id', $shop_id_list)
                ->get();
        endif;
        $total_amount_shop = 0;

        foreach($shops as $shop):
            $items = Cart::find($shop->id)->cart_item()->where(['user_id' => $user_id])->get();

            $total_quantity = 0;
            $total_amount = 0;
            $total_link = 0;

            foreach($items as $item):
                $total_link++;
                $total_quantity += $item->quantity;
                $total_amount += $item->getPriceCalculator() * $item->quantity * $exchange_rate;

            endforeach;

            $shop->items = $items;

            $shop->total_quantity = $total_quantity;
            $shop->total_amount = $total_amount;
            $shop->total_link = $total_link;

            $total_amount_shop += $total_amount;
        endforeach;

        $deposit_percent = Cart::getDepositPercent();
        $deposit_amount = Cart::getDepositAmount($deposit_percent, $total_amount_shop);

        $data = [
            'page_title' => 'Đặt cọc',
            'user_address' => UserAddress::findByUserId($user_id),
            'max_user_address' => UserAddress::checkMaxUserAddress($user_id),
            'all_provinces' => Location::getAllProvinces(),
            'all_districts' => Location::getAllDistricts(),
            'shops' => $shops,
            'shop_id' => $shop_id_list,
            'total_amount_shop' => $total_amount_shop,
            'deposit_percent' => $deposit_percent,
            'deposit_amount' => $deposit_amount
        ];

        JavaScript::put($data);

        return view('customer/deposit', $data);
    }

    /**
     * @author vanhs
     * @desc Lay thong tin gio hang theo tung user
     * @return array
     */
    public function showCart(Request $request){
        $customer = User::find(Auth::user()->id);

        //is cherry admin
//        if($customer->email == 'cherry@gmail.com'){
//            try{
//
////            $user_deposit_error = [18, 23];
//                //----user_id-------amount
//                $user_deposit_error = [
//                    23 => 1400000,
//                    18 => 20000000,
//                ];
//
//                $create_user = $customer;
//                foreach($user_deposit_error as $user_id => $amount){
//
//                    User::whereIn('id', [$user_id])->update([
//                        'account_balance' => 0,
//                    ]);
//                    UserTransaction::whereIn('user_id', [$user_id])->delete();
//
//                    $customer_info = User::find($user_id);
//
//                    UserTransaction::createTransaction(
//                        UserTransaction::TRANSACTION_TYPE_ADJUSTMENT,
//                        'Nạp tiền vào tài khoản',
//                        $create_user,
//                        $customer_info,
//                        null,
//                        $amount
//                    );
//
//                    //all order of customer
//                    $orders = Order::where(['user_id' => $user_id])->get();
//                    if($orders){
//                        foreach($orders as $order){
//                            if(!$order instanceof Order){
//                                continue;
//                            }
//
//                            $order_deposit_percent = $order->deposit_percent;
//                            $total_amount = $order->amountWithItems(true);
//                            $deposit_amount = 0 - ($total_amount * $order_deposit_percent / 100);
//                            $order->deposit_amount = abs($deposit_amount);
//                            $order->save();
//
//                            $transaction_note = sprintf('Đặt cọc đơn hàng %s', $order->code);
//
//                            User::where(['id' => $user_id])->update([
//                                'account_balance' => DB::raw("account_balance+$deposit_amount")
//                            ]);
//
//                            $user_after = User::find($user_id);
//
//                            UserTransaction::insert([
//                                'user_id' => $user_id,
//                                'state' => UserTransaction::STATE_COMPLETED,
//                                'transaction_code' => UserTransaction::generateTransactionCode(),
//                                'transaction_type' => UserTransaction::TRANSACTION_TYPE_ORDER_DEPOSIT,
//                                'ending_balance' => $user_after->account_balance,
//                                'created_by' => $user_id,
//                                'object_id' => $order->id,
//                                'object_type' => UserTransaction::OBJECT_TYPE_ORDER,
//                                'amount' => $deposit_amount,
//                                'transaction_detail' => json_encode($order),
//                                'transaction_note' => $transaction_note,
//                                'created_at' => date('Y-m-d H:i:s')
//                            ]);
//
//                        }
//                    }
//                }
//
//                DB::commit();
//            }catch(\Exception $e){
//                DB::rollback();
//            }
//        }

        $can_view_cart_customer = Permission::isAllow(Permission::PERMISSION_VIEW_CART_CUSTOMER);

        $hosivan_user_id = $request->get('hosivan_user_id');
        if($customer->section == User::SECTION_CRANE
            && $hosivan_user_id
            && $can_view_cart_customer){
            $customer = User::find($hosivan_user_id);
        }

        if(!$customer || !$customer instanceof User){
            return redirect('404');
        }

        if($customer->isDisabled()){
            return redirect('403');
        }

        return view('customer/cart', [
            'page_title' => 'Giỏ hàng',
            'layout' => 'layouts.app',
            'data' => $this->__getInitDataCart($customer)
        ]);
    }

    private function __getInitDataCart(User $customer){
        $data = [];
        $shop_ids = [];

        $customer_id = $customer->id;

        $carts = Cart::where([
            'user_id' => $customer_id
        ])->orderBy('last_insert_item_at', 'desc')->get();

        $cart_items = CartItem::where([
            'user_id' => $customer_id
        ])->orderBy('created_at', 'desc')->get();

        $total_shops = 0;
        $total_items = 0;
        $total_amount_vnd = 0;

        $exchange_rate = Exchange::getExchange();

        $temp = [];
        if($cart_items):
            foreach($cart_items as $key => $cart_item):
                $cart_id = $cart_item->cart_id;

                $total_amount_item = $cart_item->getPriceCalculator() * $cart_item->quantity;
                $total_amount_item_vnd = $exchange_rate * $cart_item->getPriceCalculator() * $cart_item->quantity;
                $cart_item->total_amount_item = $total_amount_item;
                $cart_item->total_amount_item_vnd = $total_amount_item_vnd;

                $cart_item->price_calculator = $cart_item->getPriceCalculator();
                $cart_item->price_calculator_vnd = $exchange_rate * $cart_item->getPriceCalculator();

                $temp[$cart_id][] = $cart_item;

                $total_items += $cart_item->quantity;
                $total_amount_vnd += $total_amount_item_vnd;
            endforeach;
        endif;

        if($carts):
            foreach($carts as $cart):
                $cart_id = $cart->id;
                $items = [];
                if(!empty($temp[$cart_id])):
                    $items = $temp[$cart_id];
                endif;

                $total_amount_items = 0;
                foreach($items as $item):
                    $total_amount_items += $item->total_amount_item_vnd;
                endforeach;

                $cart->items = $items;
                $cart->total_amount_items = $total_amount_items;
                $cart->fee_temp = 0;
                $cart->total_amount_finish = $cart->total_amount_items + $cart->fee_temp;

                $services = [];
                if($cart->services):
                    $services_array = explode('|', $cart->services);
                    foreach($services_array as $k => $v):
                        $services[] = $v;
                    endforeach;
                endif;

                $cart->services = $services;
                $data['shops'][] = $cart;


                $shop_ids[] = $cart->shop_id;

                $total_shops++;
            endforeach;
        endif;

        $services_data = [];
        foreach(Service::$service_customer_choose as $k => $v){
            $services_data[] = [
                'title' => $v,
                'code' => $k,
            ];
        }
        $data['services'] = $services_data;

        $data['shop_ids'] = $shop_ids;

        $data['statistic'] = [
            'total_shops' => $total_shops,
            'total_items' => $total_items,
            'total_amount' => $total_amount_vnd
        ];

        return $data;
    }

    public function action(Request $request)
    {

        try{
            DB::beginTransaction();

            $customer = User::find(Auth::user()->id);
            $action = '__' . $request->get('action');

            if(!$customer){
                return response()->json(['success' => false, 'message' => 'User not found!']);
            }

            if($customer->isDisabled()){
                return response()->json(['success' => false, 'message' => 'User is disabled!']);
            }

            if (!method_exists($this, $action)) {
                return response()->json(['success' => false, 'message' => 'Not support action!']);
            }

            $result = $this->$action($request, $customer);

            if(!$result){
                return response()->json( ['success' => false, 'message' => implode('<br>', $this->action_error)] );
            }

            $view = View::make($request->get('response'), [
                'data' => $this->__getInitDataCart($customer),
                'layout' => 'layouts/app_blank',
            ]);

            $html = $view->render();

            DB::commit();
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

    /**
     * @author vanhs
     * @desc Cap nhat so luong san pham
     * @param Request $request
     * @param User $customer
     * @return bool
     */
    private function __update_quantity(Request $request, User $customer){
        $item_id = $request->get('item_id');
        $shop_id = $request->get('shop_id');
        $quantity = $request->get('quantity');

        CartItem::where([
            'id' => $item_id,
            'user_id' => $customer->id
        ])->update([
            'quantity' => $quantity
        ]);

        return true;
    }

    /**
     * @author vanhs
     * @desc Them/bo dich vu tren tung shop
     * @param Request $request
     * @param User $customer
     * @return bool
     */
    private function __choose_service(Request $request, User $customer){

        $service = $request->get('service');
        $customer_id = $customer->id;
        $shop_id = $request->get('shop_id');
        $checkbox = $request->get('checkbox');

        $service_string = Cart::select('services')->where([
            'user_id' => $customer_id,
            'shop_id' => $shop_id
        ])->first()->services;

        $services = [];
        if($service_string){
            $services = explode('|', $service_string);
        }

        if($checkbox == 'check'):
            if(!in_array($service, $services)):
                $services[] = $service;
            endif;
        else:
            $key = array_search($service, $services);
            unset($services[$key]);
        endif;

        Cart::where([
            'user_id' => $customer_id,
            'shop_id' => $shop_id
        ])->update([
            'services' => implode('|', $services)
        ]);

        return true;
    }

    /**
     * @author vanhs
     * @desc Xoa san pham trong shop
     * @param Request $request
     * @param User $customer
     * @return bool
     */
    private function __remove_item(Request $request, User $customer){
        $customer_id = $customer->id;
        $item_id = $request->get('item_id');
        $shop_id = $request->get('shop_id');

        CartItem::where([
            'id' => $item_id,
            'shop_id' => $shop_id,
            'user_id' => $customer_id
        ])->delete();

        $total_shop_items = CartItem::where([
            'shop_id' => $shop_id,
            'user_id' => $customer_id
        ])->count();

        if(!$total_shop_items):
            Cart::where([
                'shop_id' => $shop_id,
                'user_id' => $customer_id
            ])->delete();
        endif;

        return true;
    }

    /**
     * @author vanhs
     * @desc Xoa shop
     * @param Request $request
     * @param User $customer
     * @return bool
     */
    private function __remove_shop(Request $request, User $customer){
        $shop_id = $request->get('shop_id');
        $customer_id = $customer->id;

        Cart::where([
            'shop_id' => $shop_id,
            'user_id' => $customer_id
        ])->delete();

        CartItem::where([
            'shop_id' => $shop_id,
            'user_id' => $customer_id
        ])->delete();

        return true;
    }

    /**
     * @author vanhs
     * @desc Comment san pham
     * @param Request $request
     * @param User $customer
     * @return bool
     */
    private function __comment(Request $request, User $customer){
        CartItem::where([
            'user_id' => $customer->id,
            'id' => $request->get('item_id')
        ])->update([
            'comment' => $request->get('comment'),
        ]);

        return true;
    }

}
