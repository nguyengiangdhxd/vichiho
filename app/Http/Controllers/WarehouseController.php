<?php

namespace App\Http\Controllers;

use App\Permission;
use App\User;
use App\UserWarehouse;
use App\WareHouse;
use Illuminate\Auth\Access\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class WarehouseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @author vanhs
     * @desc Xem cau hinh kho
     * @param Response $response
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function render_manually(Response $response){
        $can_execute = Permission::isAllow(Permission::PERMISSION_MANAGER_WAREHOUSE_MANUALLY_VIEW);
        if(!$can_execute):
            return redirect('403');
        endif;

//        $user_warehouse = UserWarehouse::orderBy('id', 'desc')->get();

        $users = User::where([
            'status' => User::STATUS_ACTIVE,
            'section' => User::SECTION_CUSTOMER
        ])
            ->orderBy('id', 'desc')
            ->get();

        $user_warehouse = DB::table('user_warehouse')
            ->join('warehouse', 'user_warehouse.warehouse_code', '=', 'warehouse.code')
            ->join('users', 'users.id', '=', 'user_warehouse.user_id')
            ->select('user_warehouse.*', 'warehouse.name', 'warehouse.alias', 'users.name as user_name', 'users.email')
            ->get();

        $warehouses = WareHouse::where([
            'type' => WareHouse::TYPE_DISTRIBUTION
        ])->orderBy('ordering', 'asc')->get();

        return view('warehouse_manually', [
            'page_title' => 'Cấu hình kho',
            'user_warehouse' => $user_warehouse,
            'warehouses' => $warehouses,
            'users' => $users,
            'can_add_new' => Permission::isAllow(Permission::PERMISSION_MANAGER_WAREHOUSE_MANUALLY_INSERT),
            'can_remove' => Permission::isAllow(Permission::PERMISSION_MANAGER_WAREHOUSE_MANUALLY_DELETE),
        ]);
    }

    /**
     * @author vanhs
     * @desc Them cau hinh kho
     * @param Response $response
     * @return mixed
     */
    public function insert_manually(Response $response){
        $input = Input::all();

        $user_id = $input['user_id'];
        $warehouse_code = $input['warehouse_code'];

        $error = [];

        $can_execute = Permission::isAllow(Permission::PERMISSION_MANAGER_WAREHOUSE_MANUALLY_INSERT);
        if(!$can_execute):
            return response()->json(['success' => false, 'message' => 'not permission']);
        endif;

        if(empty($user_id)):
            $error[] = 'Vui lòng chọn khách hàng!';
        endif;

        if(empty($warehouse_code)):
            $error[] = 'Vui lòng chọn kho!';
        endif;

        $exists = UserWarehouse::where([
            'user_id' => $user_id,
        ])->count();

        if($exists):
            $error[] = sprintf('User này đã được cấu hình kho!');
        endif;

        if(count($error)):
            return response()->json(['success' => false, 'message' => implode('<br>', $error)]);
        endif;

        UserWarehouse::insert([
            'user_id' => $user_id,
            'warehouse_code' => $warehouse_code,
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => Auth::user()->id,
        ]);

        return response()->json(['success' => true, 'message' => 'insert success']);
    }

    /**
     * @author vanhs
     * @desc Xoa cau hinh kho
     * @param Response $response
     * @return mixed
     */
    public function delete_manually(Response $response){
        $input = Input::all();

        $user_id = $input['user_id'];
        $warehouse_code = $input['warehouse_code'];

        $can_execute = Permission::isAllow(Permission::PERMISSION_MANAGER_WAREHOUSE_MANUALLY_DELETE);
        if(!$can_execute):
            return response()->json(['success' => false, 'message' => 'not permission']);
        endif;

        UserWarehouse::where([
            'user_id' => $user_id,
            'warehouse_code' => $warehouse_code
        ])->delete();

        return response()->json(['success' => true, 'message' => 'insert success']);
    }

    /**
     * @author vanhs
     * @desc Hien thi thong tin danh sach kho
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function render(Request $request){
        $can_execute = Permission::isAllow(Permission::PERMISSION_MANAGER_WAREHOUSE);
        if(!$can_execute):
            return redirect('403');
        endif;

        $data = WareHouse::all();

        return view('warehouse', [
            'page_title' => 'Quản lý kho hàng',
            'data' => $data
        ]);
    }

    /**
     * @author vanhs
     * @desc Tao moi kho
     * @param Request $request
     * @return mixed
     */
    public function insert(Request $request){
        $input_data = Input::all();

        $can_execute = Permission::isAllow(Permission::PERMISSION_MANAGER_WAREHOUSE);
        if(!$can_execute):
            return response()->json(['success' => false, 'message' => 'not permission']);
        endif;

        $error = [];
        if(empty($input_data['code'])):
            $error[] = 'Mã kho không để trống!';
        endif;

        if(empty($input_data['alias'])):
            $error[] = 'Alias không để trống!';
        endif;

        if(empty($input_data['name'])):
            $error[] = 'Tên kho không để trống!';
        endif;

//        if(empty($input_data['type'])):
//            $error[] = 'Loại không để trống!';
//        endif;

        if(count($error)):
            return response()->json(['success' => false, 'message' => implode('<br>', $error)]);
        endif;

        $exists_warehouse_code = WareHouse::where([
            'code' => $input_data['code']
        ])->count();

        if($exists_warehouse_code):
            return response()->json(['success' => false, 'message' => sprintf('Mã kho %s đã tồn tại!', $input_data['code'])]);
        endif;

        WareHouse::insert([
            'code' => $input_data['code'],
            'alias' => $input_data['alias'],
            'name' => $input_data['name'],
            'type' => $input_data['type'],
            'ordering' => empty($input_data['ordering']) ? 0 : $input_data['ordering'],
            'description' => $input_data['description'],
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return response()->json(['success' => true, 'message' => 'insert success']);
    }

    /**
     * @author vanhs
     * @desc Xoa kho
     * @param Request $request
     * @return mixed
     */
    public function delete(Request $request){
        $input_data = Input::all();

        $can_execute = Permission::isAllow(Permission::PERMISSION_MANAGER_WAREHOUSE);
        if(!$can_execute):
            return response()->json(['success' => false, 'message' => 'not permission']);
        endif;

        $id = $input_data['id'];

        if($id):
            WareHouse::where(['id' => $id])->delete();
        endif;

        return response()->json(['success' => true, 'message' => 'delete success']);
    }
}
