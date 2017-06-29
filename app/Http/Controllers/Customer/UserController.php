<?php

namespace App\Http\Controllers\Customer;

use App\Permission;
use App\User;
use App\UserRefer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @author vanhs
     * @desc Ham lay thong tin hien thi trong trang chi tiet nhan vien
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function detail(Request $request){

        $current_user = User::find(Auth::user()->id);
        $user_id = $request->route('id');
        $user = User::find($user_id);

        if(!$user || !$user instanceof User):
            return redirect('404');
        endif;

        if($current_user->id != $user->id){
            return redirect('403');
        }

        $user_refer = $this->__user_refer($user);

        return view('customer/user_detail', [
            'page_title' => "Thông tin cá nhân",
            'user' => $user,
            'user_id' => $user_id,
            'user_mobiles' => $user->mobile,
            'user_refer' => $user_refer,
            'permission' => [
                'can_add_mobile' => true,
                'can_remove_mobile' => true,
                'can_edit_user' => true,
            ]
        ]);

    }

    private function __user_refer(User $user){
        $user_refer_data = UserRefer::where([
            'user_refer_id' => $user->id
        ]);
        $user_refer_total = (int)$user_refer_data->count();
        $user_refer_data_list = [];
        if($user_refer_total > 0){
            $user_refer_data_object = $user_refer_data->get();
            foreach($user_refer_data_object as $user_refer_data_object_item){
                if(!$user_refer_data_object_item instanceof UserRefer){
                    continue;
                }
                $user_refer_data_object_item->user = User::find($user_refer_data_object_item->user_id);
                $user_refer_data_object_item->user_refer = User::find($user_refer_data_object_item->user_refer_id);
                $user_refer_data_list[] = $user_refer_data_object_item;
            }
        }

        return [
            'link' => url('register?user_refer=' . $user->code),
            'total' => $user_refer_total,
            'data' => $user_refer_data_list
        ];
    }

    /**
     * @author vanhs
     * @desc Them so dien thoai cho user
     * @param Request $request
     * @return mixed
     */
    public function add_user_phone(Request $request){
        $user_phone = $request->get('user_phone');
        $user_id = $request->get('user_id');

        if(!$user_phone):
            return Response::json(['success' => false, 'message' => 'Số điện thoại không hợp lệ!']);
        endif;

        $user = User::find($user_id);

        if(!$user || !$user instanceof User):
            return Response::json(['success' => false, 'message' => 'User không hợp lệ !']);
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
    public function delete_user_phone(Request $request){
        $user_phone = $request->get('user_phone');
        $user_phone_id = $request->get('user_phone_id');
        $user_id = $request->get('user_id');

        $user = User::find($user_id);

        if(!$user || !$user instanceof User):
            return Response::json(['success' => false, 'message' => 'User not found!']);
        endif;

        $user->deleteMobile($user_phone);

        return Response::json(['success' => true, 'message' => 'delete success']);
    }

    /**
     * @author vanhs
     * @desc Cap nhat thong tin nguoi dung
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update_user(Request $request){
        $user_id = $request->get('id');
        $current_user_id = Auth::user()->id;

        if($user_id != $current_user_id):
            return redirect('403');
        endif;

        $password = trim($request->get('password'));
        $name = $request->get('name');

        $user = User::find($user_id);

        if(!$user || !$user instanceof User):
            return redirect('404');
        endif;

        $rules['name'] = 'required';

        if($password):
            $rules['password'] = 'required|min:6';
            $user->password = bcrypt($password);
        endif;

        $this->validate($request, $rules);

        if(!empty($name)):
            $user->name = $name;
        endif;

        $user->updated_at = date('Y-m-d H:i:s');
        $user->save();

        return redirect("nhan-vien/{$user_id}");
    }

    /**
     * @author vanhs
     * @desc Lay du lieu can thiet de hien thi o man hinh form chinh sua nhan vien
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function get_user(Request $request){
        $user_id = $request->route('id');
        $current_user_id = Auth::user()->id;

        if($user_id != $current_user_id){
            return redirect('403');
        }

        $user = User::find($user_id);
        if(!$user || !$user instanceof User):
            return redirect('404');
        endif;

        return view('customer/user_form', [
            'page_title' => "Sửa thông tin cá nhân",
            'user_id' => $user_id,
            'user' => $user
        ]);
    }
}
