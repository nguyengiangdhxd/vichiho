<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UserTransaction extends Model
{
    protected $table = 'user_transaction';

    const STATE_PENDING = 'PENDING';
    const STATE_COMPLETED = 'COMPLETED';
    const STATE_CANCELED = 'CANCELED';
    const STATE_REJECTED = 'REJECTED';
    const STATE_EXPIRED = 'EXPIRED';

    const TRANSACTION_TYPE_DEPOSIT = 'DEPOSIT';//nạp tiền
    const TRANSACTION_TYPE_WITHDRAWAL = 'WITHDRAWAL';//rút tiền

    const TRANSACTION_TYPE_ORDER_DEPOSIT = 'ORDER_DEPOSIT';//đặt cọc
    const TRANSACTION_TYPE_DEPOSIT_ADJUSTMENT = 'DEPOSIT_ADJUSTMENT'; //điều chỉnh đặt cọc trên đơn

    const TRANSACTION_TYPE_ORDER_PAYMENT = 'ORDER_PAYMENT';//thanh toán trên đơn
    const TRANSACTION_TYPE_PAYMENT = 'PAYMENT';//truy thu
    const TRANSACTION_TYPE_PROMOTION = 'PROMOTION';//khuyến mại
    const TRANSACTION_TYPE_GIFT = 'GIFT';//quà tặng
    const TRANSACTION_TYPE_REFUND = 'REFUND';//trả lại theo đơn
    const TRANSACTION_TYPE_REFUND_COMPLAINT = 'REFUND_COMPLAINT';//trả lại theo khiếu nại
    const TRANSACTION_TYPE_ADJUSTMENT = 'ADJUSTMENT';//giao dịch điều chỉnh

    const OBJECT_TYPE_ADJUSTMENT = 'ADJUSTMENT';
    const OBJECT_TYPE_ORDER = 'ORDER';
    const OBJECT_TYPE_DELIVERY = 'DELIVERY';
    const OBJECT_TYPE_DOMESTIC_SHIPPING = 'DOMESTIC_SHIPPING';
    const OBJECT_TYPE_USER = 'USER';
    const OBJECT_TYPE = 'USER_TRANSACTION';

    public static $transaction_type = array(
//        self::TRANSACTION_TYPE_DEPOSIT => 'Nạp tiền',
        self::TRANSACTION_TYPE_ORDER_DEPOSIT => 'Đặt cọc',
        self::TRANSACTION_TYPE_ORDER_PAYMENT => 'Thanh toán',
        self::TRANSACTION_TYPE_PAYMENT => 'Truy thu',
        self::TRANSACTION_TYPE_REFUND => 'Trả lại',
//        self::TRANSACTION_TYPE_REFUND_COMPLAINT => 'Trả lại theo khiếu nại',
//        self::TRANSACTION_TYPE_PROMOTION => 'Khuyến mại',
//        self::TRANSACTION_TYPE_WITHDRAWAL => 'Rút tiền',
        self::TRANSACTION_TYPE_ADJUSTMENT => "Điều chỉnh",
        self::TRANSACTION_TYPE_GIFT => "Quà tặng",
        self::TRANSACTION_TYPE_DEPOSIT_ADJUSTMENT => "Điều chỉnh đặt cọc"
    );

    public static $transaction_adjustment = array(
        self::TRANSACTION_TYPE_ADJUSTMENT => "Điều chỉnh",
        self::TRANSACTION_TYPE_PAYMENT => 'Truy thu',
        self::TRANSACTION_TYPE_REFUND => 'Trả lại',
        self::TRANSACTION_TYPE_GIFT => "Quà tặng"
    );

    public static $transaction_adjustment_object = [
        self::OBJECT_TYPE_ORDER => 'Đơn hàng'
    ];

    public static $transaction_adjustment_type = [
        'positive' => 'Điều chỉnh dương',
        'negative' => 'Điều chỉnh âm'
    ];

    public static $transaction_state = array(
        self::STATE_CANCELED => 'Hủy bỏ',
        self::STATE_COMPLETED => 'Hoàn thành',
        self::STATE_PENDING => 'Chờ duyệt',
        self::STATE_REJECTED => 'Từ chối',
        self::STATE_EXPIRED => 'Quá hạn',
    );

    /**
     * @desc Tao ma giao dich, quy tac [ngay] [thang] [nam] [gio] [phut] [giay] [so ngau nhien 1000 - 9999]
     * @return string
     */
    public static function generateTransactionCode() {
        $now = new \DateTime();

        $day_part = $now->format("YmdHis");

        do {
            $rnd = rand(1000,9999);
            $random_part = $rnd;
            $code = $day_part.$random_part;

            $existed = self::select('id')
                ->where(['transaction_code' => $code])
                ->first();
        }
        while ($existed);

        return $code;
    }

    /**
     * generate checksum
     * @param $data
     * @param null $salt
     * @return string
     */
    public function generateChecksum($data, $salt = null) {
        if (null == $salt) {
            $salt = md5(uniqid());
        }

        ksort($data);
        return $salt. md5($salt .json_encode($data));
    }

    /**
     * create checksum
     * @return string
     */
    public function createChecksum() {

    }

    /**
     * check Checksum match with owner data
     * @return string
     */
    public function checkChecksum() {

    }

    public static function getPaymentOrder(User $customer, Order $order){
        return DB::table('user_transaction')
            ->select(DB::raw('SUM(amount) as total_amount'))
            ->where([
                'user_id' => $customer->id,
                'state' => self::STATE_COMPLETED,
                'object_id' => $order->id,
                'object_type' => self::OBJECT_TYPE_ORDER,

            ])
            ->first()->total_amount;
    }

    /**
     * @author vanhs
     * @desc Tinh tong so tien khach da dat coc cho don hang de tra lai, khi an huy don hang
     * @param User $customer
     * @param Order $order
     * @return mixed
     */
    public static function getDepositOrder(User $customer, Order $order){
        return DB::table('user_transaction')
            ->select(DB::raw('SUM(amount) as total_amount'))
            ->where([
                'user_id' => $customer->id,
                'state' => self::STATE_COMPLETED,
                'object_id' => $order->id,
                'object_type' => self::OBJECT_TYPE_ORDER,

            ])
            ->whereIn('transaction_type', [
                self::TRANSACTION_TYPE_ORDER_DEPOSIT,
                self::TRANSACTION_TYPE_DEPOSIT_ADJUSTMENT
            ])
            ->first()->total_amount;
    }

    public static function getCustomerPaymentOrder(User $customer, Order $order){
        return DB::table('user_transaction')
            ->select(DB::raw('SUM(amount) as total_amount'))
            ->where([
                'user_id' => $customer->id,
                'state' => self::STATE_COMPLETED,
                'object_id' => $order->id,
                'object_type' => self::OBJECT_TYPE_ORDER,

            ])
            ->first()->total_amount;
    }

    public static function createTransaction($transaction_type, $transaction_note, User $create, User $customer, $object, $amount){
        try{
            DB::beginTransaction();

            $customer->updateAccountBalance($amount, $customer->id);

            $customer_after_change = User::find($customer->id);

            $transaction_code = self::generateTransactionCode();

            $object_id = null;
            $object_type = null;
            $transaction_detail = null;
            if($object instanceof Order){
                $object_id = $object->id;
                $object_type = self::OBJECT_TYPE_ORDER;
            }

            $user_transaction = new self();
            $user_transaction->user_id = $customer->id;
            $user_transaction->state = self::STATE_COMPLETED;
            $user_transaction->transaction_code = $transaction_code;
            $user_transaction->transaction_type = $transaction_type;
            $user_transaction->amount = $amount;
            $user_transaction->ending_balance = $customer_after_change->account_balance;
            $user_transaction->created_by = $create->id;
            if($object_id){
                $user_transaction->object_id = $object_id;
            }
            if($object_type){
                $user_transaction->object_type = $object_type;
            }

            $user_transaction->transaction_detail = json_encode($object);
            $user_transaction->transaction_note = $transaction_note;
            $user_transaction->save();

            DB::commit();
            return true;
        }catch(\Exception $e){
            DB::rollback();
            return false;
        }
    }


}
