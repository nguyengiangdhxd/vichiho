<?php

namespace App\Http\Controllers\Customer;

use App\Permission;
use App\UserTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Order;
use App\Http\Controllers\Controller;

class UserTransactionController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');

    }

    /**
     * @author vanhs
     * @desc Danh sach giao dich
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getTransactions(){

        $per_page = 20;
        $transactions = UserTransaction::where([
                'user_id' => Auth::user()->id,
                'state' => UserTransaction::STATE_COMPLETED
            ])
            ->orderBy('id', 'desc')
            ->paginate($per_page);

        return view('customer/transactions', [
            'page_title' => 'Lịch sử giao dịch ',
            'transactions' => $transactions
        ]);
    }

}
