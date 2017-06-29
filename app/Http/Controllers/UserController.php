<?php

namespace App\Http\Controllers;

use App\Permission;
use App\UserAddress;
use App\UserOriginalSite;
use App\UserRefer;
use App\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use App\User;
use App\UserTransaction;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @author vanhs
     * @desc Lay du lieu hien thi danh sach user mua hang site goc
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function listUserOriginalSite(){
        $can_execute = Permission::isAllow(Permission::PERMISSION_MANAGER_USER_ORIGINAL_SITE);
        if(!$can_execute):
            return redirect('403');
        endif;

        $data = UserOriginalSite::select('*')->orderBy('id', 'desc')->get();

        return view('user_original_site', [
            'page_title' => 'Quản lý user mua hàng site gốc',
            'data' => $data
        ]);
    }


    /**
     * @author vanhs
     * @desc Them user mua hang site goc
     * @param Request $request
     * @return mixed
     */
    public function addUserOriginalSite(Request $request){
        $can_execute = Permission::isAllow(Permission::PERMISSION_MANAGER_USER_ORIGINAL_SITE);
        if(!$can_execute):
            return response()->json(['success' => false, 'message' => 'not permisison']);
        endif;

        $username = $request->get('username');
        $site = $request->get('site');

        $error = [];
        if(empty($username)):
            $error[] = 'Vui lòng nhập vào user mua hàng!';
        endif;

        if(empty($site)):
            $error[] = 'Vui lòng chọn site gốc!';
        endif;

        if(count($error)):
            return response()->json(['success' => false, 'message' => implode('<br/>', $error)]);
        endif;

        $exists = UserOriginalSite::where([
            'username' => $username,
            'site' => $site
        ])->count();

        if($exists):
            return response()->json(['success' => false, 'message' => sprintf('Đã tồn tại user: %s - site: %s', $username, $site)]);
        endif;

        UserOriginalSite::insert([
            'username' => $username,
            'site' => $site,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return response()->json(['success' => true, 'message' => 'insert success']);
    }

    /**
     * @author vanhs
     * @desc Xoa user mua hang site goc
     * @param Request $request
     * @return mixed
     */
    public function removeUserOriginalSite(Request $request){
        $can_execute = Permission::isAllow(Permission::PERMISSION_MANAGER_USER_ORIGINAL_SITE);
        if(!$can_execute):
            return response()->json(['success' => false, 'message' => 'not permisison']);
        endif;

        $username = $request->get('username');
        $site = $request->get('site');

        UserOriginalSite::where([
            'username' => $username,
            'site' => $site
        ])->delete();

        return response()->json(['success' => true, 'message' => 'delete success']);
    }

    /**
     * @author vanhs
     * @desc Them so dien thoai cho user
     * @param Request $request
     * @return mixed
     */
    public function addUserPhone(Request $request){
        $user_phone = $request->get('user_phone');
        $user_id = $request->get('user_id');

        $can_add_mobile = Permission::isAllow(Permission::PERMISSION_USER_ADD_MOBILE);

        if(Auth::user()->section == User::SECTION_CUSTOMER && Auth::user()->id ==$user_id):
            $can_add_mobile = true;
        endif;

        if(!$can_add_mobile):
            return Response::json(['success' => false, 'message' => 'not permission!']);
        endif;

        if(!$user_phone):
            return Response::json(['success' => false, 'message' => 'Số điện thoại không hợp lệ !']);
        endif;

        $user = User::find($user_id);

        if(!$user):
            return Response::json(['success' => false, 'message' => 'User không hợp lệ !']);
        endif;

        $can_add_mobile = Permission::isAllow(Permission::PERMISSION_USER_ADD_MOBILE);
        if(!$can_add_mobile):
            return Response::json(['success' => false, 'message' => 'not permission!']);
        endif;

        if($user->checkMaxMobile()):
            return Response::json(['success' => false, 'message' => sprintf('Bạn chỉ được thêm tối đa %s số điện thoại !', User::getMaxMobile())]);
        endif;

        if($user->checkExistsMobile($user_phone)):
            return Response::json(['success' => false, 'message' => sprintf('Số điện thoại %s đã tồn tại trên hệ thống !', $user_phone)]);
        endif;

        $user->addMobile($user_phone);

        return Response::json(['success' => true, 'message' => 'Thêm thành công.']);
    }

    /**
     * @author vanhs
     * @desc Xoa so dien thoai cua nhan vien
     * @param Request $request
     * @return mixed
     */
    public function deleteUserPhone(Request $request){
        $user_phone = $request->get('user_phone');
        $user_phone_id = $request->get('user_phone_id');
        $user_id = $request->get('user_id');

        $can_remove_mobile = Permission::isAllow(Permission::PERMISSION_USER_REMOVE_MOBILE);

        if(Auth::user()->section == User::SECTION_CUSTOMER && Auth::user()->id ==$user_id):
            $can_remove_mobile = true;
        endif;

        if(!$can_remove_mobile):
            return Response::json(['success' => false, 'message' => 'not permission!']);
        endif;

        $user = User::find($user_id);

        if(!$user):
            return Response::json(['success' => false, 'message' => 'User not found!']);
        endif;

        $user->deleteMobile($user_phone);

        return Response::json(['success' => true, 'message' => 'delete success']);
    }

    /**
     * @author vanhs
     * @desc Ham lay thong tin hien thi trong trang chi tiet nhan vien
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function detailUser(Request $request){

        $can_view = Permission::isAllow(Permission::PERMISSION_USER_VIEW);
        if(!$can_view):
            return redirect('403');
        endif;

        $user_id = $request->route('id');
        $user = User::find($user_id);

        if(!$user):
            return redirect('404');
        endif;

        $transactions = UserTransaction::where([
                'user_id' => $user_id
            ])
            ->orderBy('id', 'desc')
            ->get();

        $can_add_mobile = Permission::isAllow(Permission::PERMISSION_USER_ADD_MOBILE);
        $can_remove_mobile = Permission::isAllow(Permission::PERMISSION_USER_REMOVE_MOBILE);
        $can_edit_user = Permission::isAllow(Permission::PERMISSION_USER_EDIT);

        if(Auth::user()->section == User::SECTION_CUSTOMER && Auth::user()->id ==$user_id):
            $can_add_mobile = $can_remove_mobile = $can_edit_user = true;
        endif;

        $user_refer_data = UserRefer::where([
            'user_id' => $user->id
        ]);
        $user_refer_total = (int)$user_refer_data->count();
        $user_refer_data = $user_refer_data->get();

        $user_refer = [
            'link' => url('register', $user->code),
            'total' => $user_refer_total,
            'data' => $user_refer_data
        ];

        return view('user_detail', [
            'page_title' => "Thông tin nhân viên [" . $user->email . "]",
            'user' => $user,
            'user_id' => $user_id,
            'transactions' => $transactions,
            'user_mobiles' => $user->mobile,
            'user_refer' => $user_refer,
            'permission' => [
                'can_add_mobile' => $can_add_mobile,
                'can_remove_mobile' => $can_remove_mobile,
                'can_edit_user' => $can_edit_user,
            ]
        ]);

    }

    /**
     * @author vanhs
     * @desc Cap nhat thong tin nguoi dung
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function updateUser(Request $request){
        $user_id = $request->get('id');

        $can_edit = Permission::isAllow(Permission::PERMISSION_USER_EDIT);

        if(Auth::user()->section == User::SECTION_CUSTOMER && Auth::user()->id == $user_id):
            $can_edit = true;
        endif;

        if(!$can_edit):
            return redirect('403');
        endif;


        $password = trim($request->get('password'));
        $name = $request->get('name');

        $user = User::find($user_id);

        if(!$user):
            return redirect('404');
        endif;

        $rules['name'] = 'required';

        if($password):
            $rules['password'] = 'required|min:6';
            $user->password = bcrypt($password);
        endif;

        $this->validate($request, $rules);

        $user_section_old = $user->section;
        $user_section_new = $request->get('section');

        if(!empty($name)):
            $user->name = $name;
        endif;

        if(!($user_section_old == User::SECTION_CRANE
            && $user_section_new == User::SECTION_CUSTOMER)):
            $user->section = $request->get('section');
        endif;

        $user->status = $request->get('status');
        $user->updated_at = date('Y-m-d H:i:s');
        $user->save();

        return redirect("user/detail/{$user_id}");
    }

    /**
     * @author vanhs
     * @desc Lay du lieu can thiet de hien thi o man hinh form chinh sua nhan vien
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function getUser(Request $request){
        $user_id = $request->route('id');

        $can_edit = Permission::isAllow(Permission::PERMISSION_USER_EDIT);

        if(Auth::user()->section == User::SECTION_CUSTOMER && Auth::user()->id == $user_id):
            $can_edit = true;
        endif;

        if(!$can_edit):
            return redirect('403');
        endif;

        $user = User::find($user_id);
        if(!$user):
            return redirect('404');
        endif;

        return view('user_form', [
            'page_title' => "Sửa thông tin nhân viên [" . $user['email'] . "]",
            'section_list' => User::$section_list,
            'status_list' => User::$status_list,
            'user_id' => $user_id,
            'user' => $user
        ]);
    }

    /**
     * @author vanhs
     * @desc Ham lay thong tin hien thi danh sach nhan vien
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getUsers(Request $request){
        $can_view_cart_customer = Permission::isAllow(Permission::PERMISSION_VIEW_CART_CUSTOMER);

        $can_view = Permission::isAllow(Permission::PERMISSION_USER_VIEW_LIST);
        if(!$can_view):
            return redirect('403');
        endif;

        $condition = input::all();

        $per_page = 20;
        if($request->get('per_page')):
            $per_page = $request->get('per_page');
        endif;

        $where = [];

        if(!empty($condition['code'])){
            $where['code'] = $condition['code'];
        }

        if(!empty($condition['email'])){
            $where['email'] = $condition['email'];
        }

        if(!empty($condition['section'])){
            $where['section'] = $condition['section'];
        }

        if(!empty($condition['status'])){
            $where['status'] = $condition['status'];
        }

        $users = User::where($where);
        $users = $users->orderBy('id', 'desc');
        $total_users = $users->count();
        $users = $users->paginate($per_page);

        return view('users', [
            'page_title' => 'Danh sách nhân viên ',
            'users' => $users,
            'total_users' => $total_users,
            'can_view_cart_customer' => $can_view_cart_customer,
            'condition' => $condition,
        ]);
    }

}
