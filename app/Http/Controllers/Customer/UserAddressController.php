<?php

namespace App\Http\Controllers\Customer;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Response;
use App\UserAddress;
use App\Location;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserAddressController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @author vanhs
     * @desc Them dia chi cho khach hang
     * @param Request $request
     * @return mixed
     */
    public function addNewUserAddress(Request $request){
        $send_data = $request->all();

        $messages = [
            'province_id.required' => 'Tỉnh thành không để trống!',
            'district_id.required' => 'Quận huyện không để trống!',
            'detail.required' => 'Địa chỉ không để trống!',
            'reciver_name.required' => 'Tên người nhận không để trống!',
            'reciver_phone.required' => 'Sô điện thoại không để trống!',
            'reciver_phone.numeric' => 'Số điện thoại không hợp lệ!',
        ];

        $validator = Validator::make($send_data, [
            'province_id' => 'required',
            'district_id' => 'required',
            'detail' => 'required',
            'reciver_name' => 'required',
            'reciver_phone' => 'required|numeric'
        ], $messages);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return Response::json(array('success' => false, 'message' => implode('<br>', $errors) ));
        }

        if(!Location::checkProvinceContainDistrict($send_data['province_id'], $send_data['district_id'])):
            return Response::json(array('success' => false, 'message' => "Dữ liệu Thành Phố hoặc Quận Huyện không chính xác!" ));
        endif;

        $current_user_id = Auth::user()->id;
        $send_data['user_id'] = $current_user_id;

        if(!$send_data['user_address_id'] && !UserAddress::checkMaxUserAddress($current_user_id)):
            return Response::json(array('success' => false, 'message' => sprintf('Bạn chỉ có thể thêm tối đa %s địa chỉ!', UserAddress::getUserAddressMax()) ));
        endif;

        UserAddress::addNewUserAddress($send_data);

        return Response::json(array('success' => true));
    }

    /**
     * @author vanhs
     * @desc Thiet lap dia chi mac dinh cho khach hang
     * @param Request $request
     * @return mixed
     */
    public function setDefaultUserAddress(Request $request){
        $id = $request->input('id');
        $current_user_id = Auth::user()->id;

        $user_address = new UserAddress();
        $user_address->setDefaultUserAddress($id, $current_user_id);

        return Response::json(array('success' => true));
    }

    /**
     * @author vanhs
     * @desc Xoa dia chi nhan hang
     * @param Request $request
     * @return mixed
     */
    public function deleteUserAddress(Request $request){
        $id = $request->input('id');
        $action = $request->input('action');
        $current_user_id = Auth::user()->id;

        switch ($action):
            case "delete":

                UserAddress::where([
                    'id' => $id,
                    'user_id' => $current_user_id
                ])->update([
                    'is_delete' => 1
                ]);

                break;
            case "update":
                break;
        endswitch;

        return Response::json(array('success' => true));
    }
}
