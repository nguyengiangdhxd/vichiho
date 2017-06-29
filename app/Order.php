<?php

namespace App;

use App\Library\ServiceFee\ServiceFactoryMethod;
use App\Library\ServiceFee\WoodCrating;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use App\OrderItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    protected $table = 'order';

    protected $factoryMethodInstance = null;

    const STATUS_DEPOSITED = 'DEPOSITED';
    const STATUS_BOUGHT = 'BOUGHT';
    const STATUS_SELLER_DELIVERY = 'SELLER_DELIVERY';
    const STATUS_RECEIVED_FROM_SELLER = 'RECEIVED_FROM_SELLER';
    const STATUS_TRANSPORTING = 'TRANSPORTING';
    const STATUS_WAITING_DELIVERY = 'WAITING_DELIVERY';
    const STATUS_DELIVERING = 'DELIVERING';
    const STATUS_RECEIVED = 'RECEIVED';
    const STATUS_CANCELLED = 'CANCELLED';

    public function __construct(array $attributes = [])
    {
        $this->factoryMethodInstance = new ServiceFactoryMethod();
        parent::__construct($attributes);
    }

    #region -- begin variable static --

    public static $statusTitle = array(
        self::STATUS_DEPOSITED => 'Đã đặt cọc',
        self::STATUS_BOUGHT => 'Đã mua hàng',
        self::STATUS_SELLER_DELIVERY => "Người bán giao",
        self::STATUS_RECEIVED_FROM_SELLER => "NhatMinh247 Nhận",
        self::STATUS_TRANSPORTING => "Vận chuyển",
        self::STATUS_WAITING_DELIVERY => "Chờ giao hàng",
        self::STATUS_DELIVERING => "Đang giao hàng",
        self::STATUS_RECEIVED => 'Đã giao hàng',
        self::STATUS_CANCELLED => "Đã hủy",
    );

    public static $fieldTime = [
        self::STATUS_DEPOSITED => 'deposited_at',
        self::STATUS_BOUGHT => 'bought_at',
        self::STATUS_SELLER_DELIVERY => "seller_delivery_at",
        self::STATUS_RECEIVED_FROM_SELLER => "received_from_seller_at",
        self::STATUS_TRANSPORTING => "transporting_at",

        self::STATUS_WAITING_DELIVERY => 'waiting_delivery_at',
        self::STATUS_DELIVERING => 'delivering_at',

        self::STATUS_RECEIVED => 'received_at',
        self::STATUS_CANCELLED => 'cancelled_at',
    ];

    public static $timeListOrderDetail = [
        'deposited_at' => 'Đặt cọc',
        'bought_at' => 'Đã mua',
        'seller_delivery_at' => 'Người bán giao',
        'received_from_seller_at' => 'NhatMinh247 Nhận',
        'transporting_at' => 'Vận chuyển',
        'waiting_delivery_at' => 'Chờ giao hàng',
        'delivering_at' => 'Đang giao hàng',
        'received_at' => 'Đã giao hàng',
        'cancelled_at' => 'Hủy đơn'
    ];

    public static $statusLevel = array(
        self::STATUS_DEPOSITED,
        self::STATUS_BOUGHT,
        self::STATUS_SELLER_DELIVERY,
        self::STATUS_RECEIVED_FROM_SELLER,
        self::STATUS_TRANSPORTING,

        self::STATUS_WAITING_DELIVERY,
        self::STATUS_DELIVERING,
        self::STATUS_RECEIVED,
        self::STATUS_CANCELLED,
    );

    public static $_endingStatus = [
        self::STATUS_RECEIVED,
        self::STATUS_CANCELLED,
    ];

    #endregion

    #region -- begin function static --

    /**
     * @author vanhs
     * @desc Lay tong so don dat coc theo ngay
     * @param $day
     * @return int
     */
    public static function getTotalDepositByDay($day){//Y-m-d
        $total = self::select('id')->where([
            [ 'deposited_at', '>=', $day . ' 00:00:00' ],
            [ 'deposited_at', '<=', $day . ' 23:59:59' ],
        ])->count();
        return $total;
    }

    public static function retrieveByCode($code){
        if(empty($code)) return null;

        return self::where([
            'code' => $code
        ])->first();
    }

    /**
     * @author vanhs
     * @desc Lay danh sach ma hoa don goc + link chi tiet don hang TQ
     * @param $site
     * @param null $original_bill
     * @return array
     */
    public static function originalBillWithLink($site = null, $original_bill = null)
    {
        if (empty($site) || empty($original_bill)) {
            return null;
        }

        $href = null;
        switch (strtolower($site)) {
            case User::SITE_TAOBAO:
                $href = sprintf("http://trade.taobao.com/trade/detail/trade_item_detail.htm?bizOrderId=%s", $original_bill);
                break;
            case User::SITE_TMALL;
                $href = sprintf("http://trade.tmall.com/detail/orderDetail.htm?bizOrderId=%s", $original_bill);
                break;
            case User::SITE_1688;
                $href = sprintf("http://trade.1688.com/order/unify_buyer_detail.htm?orderId=%s", $original_bill);
                break;
        }

        return $href;
    }

    public static function getFieldTimeByStatus($field_time){
        return self::$fieldTime[$field_time] ? self::$fieldTime[$field_time] : null;
    }

    /**
     * get Right Status
     * @param $status
     * @return array Status
     */
    public static function getAfterStatus($status){
        if($status == ''){
            return array();
        }

        $status_array = array();

        $key = array_search($status,Order::$statusLevel);

        for ($i = $key+1 ; $i < count(Order::$statusLevel) ;$i++) {
            $status_array[] = Order::$statusLevel[$i];
        }

        return $status_array;
    }

    public static function getFavicon($site){
        if(empty($site)) return null;
        $site = strtolower($site);
        return asset('images/favicon_site_china/' . $site . '.png');
    }

    public static function getStatusTitle($code){
        if(empty($code)){
            return null;
        }

        return empty(self::$statusTitle[$code]) ? '' : self::$statusTitle[$code];
    }

    /**
     * get Left Status
     * @param $status
     * @return array Status
     */
    public static function getBeforeStatus($status){
        if($status == ''){
            return array();
        }

        $status_array = array();

        $key = array_search($status, Order::$statusLevel);

        for ($i = $key-1 ; $i >= 0 ;$i--) {
            $status_array[] = Order::$statusLevel[$i];
        }

        return $status_array;
    }

    public static function findOneByIdOrCode($input){
        $order = self::find($input);

        if(!$order):
            $order = Order::where(['code' => $input])->first();
            if(!$order):
                return false;
            endif;
        endif;

        return $order;
    }

    public static function findByTransactions($order_id){
        return UserTransaction::where([
            'object_id' => $order_id,
            'object_type' => UserTransaction::OBJECT_TYPE_ORDER,
            'state' => UserTransaction::STATE_COMPLETED
        ])->orderBy('created_at', 'desc')
            ->get();
    }

    public static function findByOrderItemComments($order_id){
        return Comment::where([
            'parent_object_id' => $order_id,
            'parent_object_type' => Comment::TYPE_OBJECT_ORDER,
            'object_type' => Comment::TYPE_OBJECT_ORDER_ITEM,
            'scope' => Comment::TYPE_NONE,
        ])->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * @author vanhs
     * @desc Tao ma don hang
     * @param $user
     * @return string
     * @throws \Exception
     */
    public static function createCode($user)
    {
        if(!$user):
            throw new \Exception('User not found!');
        endif;
        $user_code = $user->code;

        //fail safe: if user deposit without user code, create one
        if (!$user_code) {
            $user_code = User::genCustomerCode();
            User::where(['id' => $user->id])->update([
                'updated_at' => date('Y-m-d H:i:s'),
                'code' => $user_code
            ]);
        }

        //remove shipping address province's code
        $current_order_no = self::select('id')->where([

            [ 'user_id', '=', $user->id ],
            [ 'created_at', '>=', date('Y-m-d') . ' 00:00:00' ],
            [ 'created_at', '<=', date('Y-m-d') . ' 23:59:59' ]

        ])->count();

        $serial_part = str_pad($current_order_no + 1, 1, '0', STR_PAD_LEFT);
        $time_part = date('d');

        $working_month_sequence = Util::getWorkingMonthSequence();

        return "{$user_code}_{$working_month_sequence}{$time_part}{$serial_part}";
    }

    #endregion

    /**
     * Is before status
     * @param $status
     * @param bool $includedCurrentStatus
     * @return bool
     */
    public function isBeforeStatus($status, $includedCurrentStatus = false)
    {
        if ($includedCurrentStatus && $this->status == $status) {
            return true;
        }

        $before_status = Order::getBeforeStatus($status);
        if (empty($before_status)) {
            return false;
        }
        if (in_array($this->status, $before_status)) {
            return true;
        }
        return false;
    }

    /**
     * Is After Status
     * @param $status
     * @param bool $includedCurrentStatus
     * @return bool
     */
    public function isAfterStatus($status, $includedCurrentStatus = false)
    {
        if ($includedCurrentStatus && $this->status == $status) {
            return true;
        }
        $after_status = Order::getAfterStatus($status);

        if (empty($after_status)) {
            return false;
        }
        if (in_array($this->status, $after_status)) {
            return true;
        }
        return false;
    }

    /**
     * @ Ham kiem tra xem don hang co phai la trang thai cuoi cung hay chua?
     * @return bool
     */
    public function isEndingStatus(){
        if( in_array($this->status, self::$_endingStatus) ){
            return true;
        }
        return false;
    }

    public function changeStatus($status, $save = true){

        $this->status = $status;

        $field_time = self::getFieldTimeByStatus($status);
        if($field_time){
            $this->$field_time = date('Y-m-d H:i:s');
        }

        if($save){
            $this->save();
        }

    }

    /**
     * @desc Lay danh sach san pham nam trong don hang
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getItemInOrder()
    {
        return OrderItem::where([
            'order_id' => $this->id
        ])->get();
    }

    /**
     * @desc Lay ra tong so san pham loai thuong
     * @return int
     */
    public function getItemNormalQuantity()
    {
        $total = 0;
        $items = $this->getItemInOrder();

        if (!empty($items)) {
            foreach ($items as $item) {
                if (!$item->checkItemAssess($this->exchange)) {
                    $total += $item->check_quantity;
                }
            }
        }

        return $total;
    }

    /**
     * @desc Lay ra tong so san pham la phu kien
     * @return int
     */
    public function getItemAssessQuantity()
    {
        $total = 0;
        $items = $this->getItemInOrder();

        if (!empty($items)) {
            foreach ($items as $item) {
                if ($item->checkItemAssess($this->exchange)) {
                    $total += $item->check_quantity;
                }
            }
        }

        return $total;
    }

    public function has_original_bill($original_bill){
        return OrderOriginalBill::where([
            'original_bill' => $original_bill,
            'order_id' => $this->id,
            'is_deleted' => 0
        ])->count();
    }

    public function has_freight_bill($freight_bill){
        return OrderFreightBill::where([
            'order_id' => $this->id,
            'freight_bill' => $freight_bill,
            'is_deleted' => 0
        ])->count();
    }

    public function exist_freight_bill(){
        return OrderFreightBill::where([
            'order_id' => $this->id,
            'is_deleted' => 0
        ])->count() > 0;
    }

    public function exist_original_bill(){
        return OrderOriginalBill::where([
                'order_id' => $this->id,
                'is_deleted' => 0
            ])->count() > 0;
    }

    public function create_freight_bill($user_id, $freight_bill){
        $order_freight_bill = new OrderFreightBill();
        $order_freight_bill->user_id = $user_id;
        $order_freight_bill->order_id = $this->id;
        $order_freight_bill->freight_bill = $freight_bill;
        return $order_freight_bill->save();
    }

    public function create_original_bill($user_id, $original_bill){
        $order_original_bill = new OrderOriginalBill();
        $order_original_bill->user_id = $user_id;
        $order_original_bill->order_id = $this->id;
        $order_original_bill->original_bill = $original_bill;
        return $order_original_bill->save();
    }

    public function getBuyingFee($total_amount_vnd){
        $service = $this->factoryMethodInstance->makeService([
            'service_code' => Service::TYPE_BUYING,
            'total_amount' => $total_amount_vnd,
            'apply_time' => $this->deposited_at
        ]);
        return $service->calculatorFee();
    }

    public function getCheckingFee(){
        $total_quantity_items_normal = $this->getItemNormalQuantity();
        $total_quantity_items_assess = $this->getItemAssessQuantity();

        $service = $this->factoryMethodInstance->makeService([
            'service_code' => Service::TYPE_CHECKING,
            'total_quantity_items_normal' => $total_quantity_items_normal,
            'total_quantity_items_assess' => $total_quantity_items_assess,
            'apply_time' => $this->deposited_at
        ]);
        return $service->calculatorFee();
    }

    public function getShippingChinaVietnam($weight){
        //-- Can nang truyen vao phai chuyen sang kg
        $service = $this->factoryMethodInstance->makeService([
            'service_code' => Service::TYPE_SHIPPING_CHINA_VIETNAM,
            'weight' => $weight,
            'destination_warehouse' => $this->destination_warehouse,
            'apply_time' => $this->deposited_at
        ]);
        return $service->calculatorFee();
    }

    public function getWoodCrating($shipping_china_vietnam_fee, $weight_manual, $weight_equivalent, $calculator_by = null){
        $data = [
            'service_code' => Service::TYPE_WOOD_CRATING,
            'fee_shipping_china_vietnam' => $shipping_china_vietnam_fee,//phi van chuyen quoc te
            'weight_manual' => $weight_manual,//can nang tinh
            'weight_equivalent' => $weight_equivalent,//can nang quy doi
            'apply_time' => $this->deposited_at
        ];

        if($calculator_by){
            $data['calculator_by'] = $calculator_by;
        }

        $service = $this->factoryMethodInstance->makeService($data);
        return $service->calculatorFee();
    }

    public static $fee_field_order_detail = [
        'amount_vnd' => 'Tiền hàng',
        'buying_fee' => 'Mua hàng',
        'domestic_shipping_fee_vnd' => 'VC nội địa TQ',
        'shipping_china_vietnam_fee' => 'VC quốc tế',
        'total_amount_all' => 'Tổng chi phí',
        'customer_payment_amount' => 'Đã thanh toán',
        'need_payment_amount' => 'Cần thanh toán',
    ];

    public function fee(User $customer, $packages = []){
        $total_amount_vnd = $this->amount(true);

        $buying_fee = $this->getBuyingFee($total_amount_vnd);
        $shipping_china_vietnam_fee = 0;

        if(count($packages)){
            foreach($packages as $package){
                if(!$package instanceof Package){
                    continue;
                }
                $shipping_china_vietnam_fee += $this->getShippingChinaVietnam($package->getWeightCalFee());
            }
        }

        $total_fee_vnd = $buying_fee + $shipping_china_vietnam_fee;
        $total_amount_all = $total_amount_vnd + $total_fee_vnd;
        $customer_payment_amount = abs(UserTransaction::getCustomerPaymentOrder($customer, $this));
        $need_payment_amount = $total_amount_all > $customer_payment_amount
            ? $total_amount_all - $customer_payment_amount : 0;

        return [
            'amount_vnd' => $total_amount_vnd,
            'domestic_shipping_fee_vnd' => $this->domestic_shipping_fee * $this->exchange_rate,
            'deposit_percent' => $this->deposit_percent,
            'deposit_amount_vnd' => $this->deposit_amount,

            'buying_fee' => $buying_fee,
            'shipping_china_vietnam_fee' => $shipping_china_vietnam_fee,

            'total_amount_all' => $total_amount_all,
            'customer_payment_amount' => $customer_payment_amount,
            'need_payment_amount' => $need_payment_amount,
        ];
    }

    public function original_bill(){
        return $this->hasMany('App\OrderOriginalBill', 'order_id');
    }

    public function freight_bill(){
        return $this->hasMany('App\OrderFreightBill', 'order_id');
    }

    public function item(){
        return $this->hasMany('App\OrderItem', 'order_id');
    }

    public function service(){
        return $this->hasMany('App\OrderService', 'order_id');
    }

    public function package(){
        return $this->hasMany('App\Package', 'order_id');
    }

    public function total_order_quantity(){
        return DB::table('order_item')
            ->select(DB::raw('SUM(order_quantity) as total_quantity'))
            ->where([
                'order_id' => $this->id
            ])
            ->first()->total_quantity;
    }

    public function total_checking_quantity(){
        return DB::table('order_item')
            ->select(DB::raw('SUM(checking_quantity) as total_quantity'))
            ->where([
                'order_id' => $this->id
            ])
            ->first()->total_quantity;
    }

    public function total_receiver_quantity(){
        return DB::table('order_item')
            ->select(DB::raw('SUM(receiver_quantity) as total_quantity'))
            ->where([
                'order_id' => $this->id
            ])
            ->first()->total_quantity;
    }

    public function existService($service_code){
        $where['service_code'] = $service_code;
        $where['order_id'] = $this->id;
        return OrderService::where($where)->count();
    }

    public function amount($vnd = false){
        if($vnd){
            return $this->amount * $this->exchange_rate;
        }else{
            return $this->amount;
        }
    }

    public function amountWithItems($vnd = false){
        $amount = 0;
        $items = $this->item()->get();
        if($items){
            foreach($items as $item){
                if(!$item || !$item instanceof OrderItem){
                    continue;
                }

                $amount_item = $item->price * $item->order_quantity;
                if($vnd){
                    $amount_item = $item->price * $item->order_quantity * $this->exchange_rate;
                }
                $amount += $amount_item;
            }
        }
        return $amount;
    }

    public function save(array $options = [])
    {
        //before save code
        $order_amount = $this->amountWithItems(true);
        $deposit_amount = Cart::getDepositAmount($this->deposit_percent, $order_amount);
        $this->deposit_amount = $deposit_amount;

        $this->amount_vnd = $this->amount * $this->exchange_rate;
        $this->domestic_shipping_fee_vnd = $this->domestic_shipping_fee * $this->exchange_rate;

        $customer = User::find($this->user_id);
        $this->payment_vnd = abs(UserTransaction::getCustomerPaymentOrder($customer, $this));
        $this->payment = $this->payment_vnd / $this->exchange_rate;

        $saved = parent::save($options); // TODO: Change the autogenerated stub
        //end save code
        return $saved;
    }

    /**
     * @author vanhs
     * @desc Chuyen trang thai don sang NhatMinh247 nhan hang
     * @return bool
     */
    public function changeOrderReceivedFromSeller($manualy = false){

        try{
            DB::beginTransaction();

            $create_user = User::find(Auth::user()->id);

            #region -- change status --
            if($this->status == self::STATUS_SELLER_DELIVERY){
                $this->changeStatus(self::STATUS_RECEIVED_FROM_SELLER, false);
                $this->save();

                $status_title_after_change = self::getStatusTitle(self::STATUS_RECEIVED_FROM_SELLER);

                $type_context = Comment::TYPE_CONTEXT_LOG;
                $message_external = sprintf("Đơn hàng chuyển sang trạng thái %s (Đã nhận hàng từ người bán, chuẩn bị vận chuyển về Việt Nam)", $status_title_after_change);
                $message_internal = sprintf("Chuyển trạng thái đơn sang %s", $status_title_after_change);
                if($manualy){
                    $type_context = Comment::TYPE_CONTEXT_ACTIVITY;
                    $message_external = sprintf("Chuyển trạng thái đơn sang %s (Đã nhận hàng từ người bán, chuẩn bị vận chuyển về Việt Nam)", $status_title_after_change);
                    $message_internal = sprintf("Chuyển trạng thái đơn sang %s", $status_title_after_change);
                }

                Comment::createComment($create_user, $this, $message_external, Comment::TYPE_EXTERNAL, $type_context);
                Comment::createComment($create_user, $this, $message_internal, Comment::TYPE_INTERNAL, $type_context);
            }
            #endregion

            DB::commit();
            return true;

        }catch(\Exception $e){
            DB::rollback();
            return false;
        }

    }

    /**
     * @author vanhs
     * @desc Kiem tra xem don hang co chon chuyen thang hay khong
     * @return bool
     */
    public function isOrderTransportStraight(){
        return true;
    }

    /**
     * @author vanhs
     * @desc Ham lay dia chi nhan hang day du cua khach hang
     * @return null
     */
    public function getCustomerReceiveAddress(){
        if(empty($this->user_address_id)){
            return null;
        }

        $user_address = UserAddress::find($this->user_address_id);
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
        return $user_address;
    }

    /**
     * @author vanhs
     * @desc phi van chuyen noi dia TQ - VND
     * @return float
     */
    public function getDomesticShippingFeeVnd(){
        return (float)($this->domestic_shipping_fee * $this->exchange_rate);
    }

    /**
     * @author vanhs
     * @desc
     * - Chuyen trang thai don sang van chuyen:
     * - Truy thu tien hang con lai + phi mua hang
     * @return bool
     */
    public function changeOrderTransporting(){
        try{
            DB::beginTransaction();
            $create_user = User::find(Auth::user()->id);
            $customer = User::find($this->user_id);

            if($this->status == self::STATUS_RECEIVED_FROM_SELLER){
//                if($this->isOrderTransportStraight()){
//                    $this->changeStatus(self::STATUS_DELIVERING, false);
//                    $this->save();
//
//                    $status_title_after_change = self::getStatusTitle(self::STATUS_DELIVERING);
//
//                    Comment::createComment($create_user, $this, sprintf("Đơn hàng chuyển sang trạng thái %s (Hàng đang trên đường đi giao cho quý khách)", $status_title_after_change), Comment::TYPE_EXTERNAL, Comment::TYPE_CONTEXT_LOG);
//                    Comment::createComment($create_user, $this, sprintf("Chuyển trạng thái đơn sang %s", $status_title_after_change), Comment::TYPE_INTERNAL, Comment::TYPE_CONTEXT_LOG);
//                }else{
//                    $this->changeStatus(self::STATUS_TRANSPORTING, false);
//                    $this->save();
//
//                    $status_title_after_change = self::getStatusTitle(self::STATUS_TRANSPORTING);
//
//                    Comment::createComment($create_user, $this, sprintf("Đơn hàng chuyển sang trạng thái %s (Bắt đầu vận chuyển về Việt Nam)", $status_title_after_change), Comment::TYPE_EXTERNAL, Comment::TYPE_CONTEXT_LOG);
//                    Comment::createComment($create_user, $this, sprintf("Chuyển trạng thái đơn sang %s", $status_title_after_change), Comment::TYPE_INTERNAL, Comment::TYPE_CONTEXT_LOG);
//                }

                $this->changeStatus(self::STATUS_TRANSPORTING, false);
                $this->save();

                $status_title_after_change = self::getStatusTitle(self::STATUS_TRANSPORTING);

                Comment::createComment($create_user, $this, sprintf("Đơn hàng chuyển sang trạng thái %s (Bắt đầu vận chuyển về Việt Nam)", $status_title_after_change), Comment::TYPE_EXTERNAL, Comment::TYPE_CONTEXT_LOG);
                Comment::createComment($create_user, $this, sprintf("Chuyển trạng thái đơn sang %s", $status_title_after_change), Comment::TYPE_INTERNAL, Comment::TYPE_CONTEXT_LOG);

                $order_amount_vnd = $this->amountWithItems(true);
                $order_buying_fee = $this->getBuyingFee($order_amount_vnd);
                $total_customer_payment = abs(UserTransaction::getCustomerPaymentOrder($customer, $this));
                $total_need_payment = ($order_amount_vnd + $order_buying_fee + $this->getDomesticShippingFeeVnd()) - $total_customer_payment;
                $total_need_payment = 0 - abs($total_need_payment);

                $message = sprintf('Hệ thống truy thu số tiền hàng còn lại sau khi đặt cọc + phí VC nội địa TQ %sđ + phí mua hàng %sđ',
                    Util::formatNumber($this->getDomesticShippingFeeVnd()),
                    Util::formatNumber($order_buying_fee));

                UserTransaction::createTransaction(
                    UserTransaction::TRANSACTION_TYPE_ORDER_PAYMENT,
                    $message,
                    $create_user,
                    $customer,
                    $this,
                    $total_need_payment
                );

                Comment::createComment($create_user, $this, $message, Comment::TYPE_EXTERNAL, Comment::TYPE_CONTEXT_LOG);
                Comment::createComment($create_user, $this, $message, Comment::TYPE_INTERNAL, Comment::TYPE_CONTEXT_LOG);

            }

            DB::commit();
            return true;
        }catch(\Exception $e){
            DB::rollback();
            return false;
        }
    }

    /**
     * @author vanhs
     * @desc Chuyen trang thai don sang cho giao hang
     * @return bool
     */
    public function changeOrderWaitingDelivery(){
        try{
            DB::beginTransaction();
            $create_user = User::find(Auth::user()->id);

            #region -- change status --
            if($this->status == self::STATUS_TRANSPORTING){
                $this->changeStatus(self::STATUS_WAITING_DELIVERY, false);
                $this->save();

                $status_title_after_change = self::getStatusTitle(self::STATUS_WAITING_DELIVERY);

                Comment::createComment($create_user, $this, sprintf("Đơn hàng chuyển sang trạng thái %s (Hàng đã về kho phân phối, sẵn sàng giao cho quý khách)", $status_title_after_change), Comment::TYPE_EXTERNAL, Comment::TYPE_CONTEXT_LOG);
                Comment::createComment($create_user, $this, sprintf("Chuyển trạng thái đơn sang %s", $status_title_after_change), Comment::TYPE_INTERNAL, Comment::TYPE_CONTEXT_LOG);
            }
            #endregion

            DB::commit();
            return true;
        }catch(\Exception $e){
            DB::rollback();
            return false;
        }
    }

    /**
     * @author vanhs
     * @desc Chuyen trang thai don sang dang giao hang
     * @return bool
     */
    public function changeOrderDelivering(){
        try{
            DB::beginTransaction();
            $create_user = User::find(Auth::user()->id);

            #region -- change status --
            if($this->status == self::STATUS_WAITING_DELIVERY){
                $this->changeStatus(self::STATUS_DELIVERING, false);
                $this->save();

                $status_title_after_change = self::getStatusTitle(self::STATUS_DELIVERING);

                Comment::createComment($create_user, $this, sprintf("Đơn hàng chuyển sang trạng thái %s (Hàng đang trên đường đi giao cho quý khách)", $status_title_after_change), Comment::TYPE_EXTERNAL, Comment::TYPE_CONTEXT_LOG);
                Comment::createComment($create_user, $this, sprintf("Chuyển trạng thái đơn sang %s", $status_title_after_change), Comment::TYPE_INTERNAL, Comment::TYPE_CONTEXT_LOG);
            }
            #endregion

            DB::commit();
            return true;
        }catch(\Exception $e){
            DB::rollback();
            return false;
        }
    }

}
