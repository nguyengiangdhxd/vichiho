<?php

namespace App\Http\Controllers;

use App\Permission;
use App\UserTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Order;

class UserTransactionController extends Controller
{
    protected $table = 'user_transaction';

    public function __construct()
    {
        $this->middleware('auth');

    }

    public function statisticTransaction(){
        $users = User::where([
            'section' => User::SECTION_CUSTOMER
        ])->orderBy('id', 'desc')->get();

        $users_data = [];

        $resultSumOrderAmount = DB::table('order')
            ->selectRaw('user_id, sum(amount_vnd) as amount_vnd')
            ->whereNotIn('status', [ Order::STATUS_CANCELLED ])
            ->groupBy('user_id')
            ->get();
        $amount_vnd = [];
        if($resultSumOrderAmount){
            foreach($resultSumOrderAmount as $resultSumOrderAmountItem){
                $amount_vnd[$resultSumOrderAmountItem->user_id] = $resultSumOrderAmountItem->amount_vnd;
            }
        }

        $resultSumOrderPayment = DB::table('order')
            ->selectRaw('user_id, sum(payment_vnd) as payment_vnd')
            ->whereNotIn('status', [ Order::STATUS_CANCELLED ])
            ->groupBy('user_id')
            ->get();
        $payment_vnd = [];
        if($resultSumOrderPayment){
            foreach($resultSumOrderPayment as $resultSumOrderPaymentItem){
                $payment_vnd[$resultSumOrderPaymentItem->user_id] = $resultSumOrderPaymentItem->payment_vnd;
            }
        }

        $total_amount_vnd = 0;
        $total_payment_vnd = 0;
        $total_need_payment_vnd = 0;

        if($users){
            foreach($users as $user){
                if(!$user instanceof User){
                    continue;
                }

                $user->amount_vnd = empty($amount_vnd[$user->id]) ? 0 : $amount_vnd[$user->id];
                $user->payment_vnd = empty($payment_vnd[$user->id]) ? 0 : $payment_vnd[$user->id];
                $user->need_payment_vnd = $user->amount_vnd - $user->payment_vnd;
                $users_data[] = $user;

                $total_amount_vnd += $user->amount_vnd;
                $total_payment_vnd += $user->payment_vnd;
                $total_need_payment_vnd += $user->need_payment_vnd;
            }
        }

        return view('statistic_transaction', [
            'page_title' => 'Thống kê tài chính',
            'users' => $users_data,
            'total' => [
                'total_amount_vnd' => $total_amount_vnd,
                'total_payment_vnd' => $total_payment_vnd,
                'total_need_payment_vnd' => $total_need_payment_vnd,
            ]
        ]);
    }

    /**
     * @author vanhs
     * @desc Danh sach giao dich
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getTransactions(){
        $can_view = Permission::isAllow(Permission::PERMISSION_TRANSACTION_VIEW);
        if(!$can_view):
            return redirect('403');
        endif;

        $can_create_transaction = Permission::isAllow(Permission::PERMISSION_TRANSACTION_CREATE);

        $condition = Input::all();

        $per_page = 20;
        $where = [];

        if(!empty($condition['customer_code'])){
            $customer = User::retrieveByCode($condition['customer_code']);
            if($customer instanceof User){
                $where['user_id'] = $customer->id;
            }else{
                $where['user_id'] = 0;
            }
        }

        if(!empty($condition['order_code'])){
            $order = Order::retrieveByCode($condition['order_code']);
            if($order instanceof Order){
                $where['object_id'] = $order->id;
            }else{
                $where['object_id'] = 0;
            }
            $where['object_type'] = UserTransaction::OBJECT_TYPE_ORDER;
        }

        if(!empty($condition['transaction_type'])){
            $where['transaction_type'] = $condition['transaction_type'];
        }

        $transactions = UserTransaction::where($where);
        $transactions = $transactions->orderBy('id', 'desc');
        $transactions = $transactions->paginate($per_page);

        return view('transactions', [
            'page_title' => 'Lịch sử giao dịch ',
            'can_create_transaction' => $can_create_transaction,
            'transactions' => $transactions,
            'condition' => $condition,
        ]);
    }


    /**
     * @author vanhs
     * @desc Kiem tra cac dieu kien dau vao khi tao giao dich
     * @param $data_insert
     * @return mixed
     */
    private function __validateBeforeCreateTransactionAdjustment($data_insert){
        $can_create_transaction = Permission::isAllow(Permission::PERMISSION_TRANSACTION_CREATE);
        if(!$can_create_transaction):
            return ['success' => false, 'message' => 'not permission'];
        endif;

        $rules = [
            'transaction_type' => 'required',
            'amount' => 'required|has_amount_value',
            'transaction_note' => 'required'
        ];

        switch ($data_insert['transaction_type']):

            case UserTransaction::TRANSACTION_TYPE_ADJUSTMENT:
                $rules['user_id'] = 'required|user_exists';
                $rules['transaction_adjustment_type'] = 'required';
                break;

            case UserTransaction::TRANSACTION_TYPE_PAYMENT:
            case UserTransaction::TRANSACTION_TYPE_REFUND:

                $rules['object_type'] = 'required';
                switch ($data_insert['object_type']){
                    case UserTransaction::OBJECT_TYPE_ORDER:
                        $rules['order_code'] = 'required|order_exists';
                        break;
                }

                break;

            case UserTransaction::TRANSACTION_TYPE_GIFT:
                $rules['user_id'] = 'required|user_exists';
                break;
            default:
                break;
        endswitch;

        $messages = [
            'transaction_type.required' => 'Loại giao dịch không để trống !',
            'amount.required' => 'Số tiền không để trống !',
            'transaction_note.required' => 'Lý do không để trống !',
            'user_id.required' => 'Vui lòng chọn khách hàng !',
            'transaction_adjustment_type.required' => 'Vui lòng chọn loại điều chỉnh !',
            'object_type.required' => 'Vui lòng chọn đối tượng !',
            'amount.has_amount_value' => 'Số tiền không hợp lệ !',
            'user_id.user_exists' => 'Tài khoản khách hàng không hợp lệ !',
            'order_code.order_exists' => 'Đơn hàng không hợp lệ !',
            'order_code.required' => 'Mã đơn hàng không để trống !',
        ];

        Validator::extend('has_amount_value', function ($attribute, $value, $parameters, $validator) {
            return $value > 0;
        });

        Validator::extend('user_exists', function ($attribute, $value, $parameters, $validator) {
            $exists = User::select('id')
                ->where([
                    'id' => $value,
//                    'status' => User::STATUS_ACTIVE,
//                    'section' => User::SECTION_CUSTOMER
                ])
                ->first();
            if($exists):
                return true;
            endif;
            return false;
        });

        Validator::extend('order_exists', function ($attribute, $value, $parameters, $validator) {
            $exists = Order::select('id')
                ->where([
                    'code' => $value
                ])
                ->first();
            if($exists):
                return true;
            endif;
            return false;
        });

        $validator = Validator::make($data_insert, $rules, $messages);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return array('success' => false, 'message' => implode('<br>', $errors) );
        }

        return ['success' => true];
    }

    public function createTransactionAdjustment(Request $request){
        try{

            $user_create = User::find(Auth::user()->id);

            $data_insert = $request->all();
            $data_insert['created_at'] = date('Y-m-d H:i:s');

            unset($data_insert['_token']);

            $validate_data = $this->__validateBeforeCreateTransactionAdjustment($data_insert);
            if(!$validate_data['success']):
                return Response::json(array('success' => false, 'message' => $validate_data['message'] ));
            endif;

            $is_ok = false;

            switch ($data_insert['transaction_type']):
                case UserTransaction::TRANSACTION_TYPE_ADJUSTMENT:

                    if($data_insert['transaction_adjustment_type'] == 'negative'):
                        $data_insert['amount'] = 0 - $data_insert['amount'];
                    endif;

                    $customer = User::find($data_insert['user_id']);

                    $is_ok = UserTransaction::createTransaction(
                        UserTransaction::TRANSACTION_TYPE_ADJUSTMENT,
                        $data_insert['transaction_note'],
                        $user_create,
                        $customer,
                        null,
                        $data_insert['amount']
                    );

                    break;

                case UserTransaction::TRANSACTION_TYPE_GIFT:

                    $customer = User::find($data_insert['user_id']);

                    $is_ok = UserTransaction::createTransaction(
                        UserTransaction::TRANSACTION_TYPE_GIFT,
                        $data_insert['transaction_note'],
                        $user_create,
                        $customer,
                        null,
                        $data_insert['amount']
                    );

                    break;

                case UserTransaction::TRANSACTION_TYPE_REFUND:
                case UserTransaction::TRANSACTION_TYPE_PAYMENT:

                    if($data_insert['transaction_type'] == UserTransaction::TRANSACTION_TYPE_PAYMENT):
                        $data_insert['amount'] = 0 - $data_insert['amount'];
                    endif;

                    $object = null;
                    switch ($data_insert['object_type']):
                        case UserTransaction::OBJECT_TYPE_ORDER:
                            $object = Order::select('*')
                                ->where([
                                    'code' => $data_insert['order_code']
                                ])
                                ->first();
                            break;
                    endswitch;

                    $customer = User::find($object->user_id);

                    $is_ok = UserTransaction::createTransaction(
                        $data_insert['transaction_type'],
                        $data_insert['transaction_note'],
                        $user_create,
                        $customer,
                        $object,
                        $data_insert['amount']
                    );

                    break;

            endswitch;

            if(!$is_ok){
                return Response::json(['success' => true, 'message' => 'insert fail!']);
            }

            DB::commit();

            return Response::json(['success' => true, 'message' => 'insert success!']);
        }catch(\Exception $e){
            DB::rollback();
            return Response::json(['success' => false, 'message' => 'insert fail!' . $e->getMessage()]);
        }
    }

    public function renderTransactionAdjustment(){
        $users_customer = User::where([
//            'status' => User::STATUS_ACTIVE,
//            'section' => User::SECTION_CUSTOMER,
        ])->orderBy('name', 'asc')->get()->toArray();

        return view('transaction_adjustment', [
            'page_title' => 'Tạo giao dịch điều chỉnh tài chính ',
            'users_customer' => $users_customer
        ]);
    }
}
