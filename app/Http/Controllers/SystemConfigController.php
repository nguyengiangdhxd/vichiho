<?php

namespace App\Http\Controllers;

use App\Permission;
use App\SystemConfig;

use App\UserRole;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use App\Role;
use App\User;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SystemConfigController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @author vanhs
     * @desc Them/bo thanh vien vao nhom
     * @param Request $request
     * @return mixed
     */
    public function updateUserRole(Request $request){
        $can_view = Permission::isAllow(Permission::PERMISSION_VIEW_ROLE);
        if(!$can_view):
            return Response::json(['success' => false, 'message' => 'not permission']);
        endif;

        $user_id = $request->get('user_id');
        $role_id = $request->get('role_id');

        $role = Role::find($role_id);

        if(!$role):
            return Response::json(['success' => false, 'message' => 'role not found']);
        endif;


        try{
            DB::beginTransaction();

            $action = $request->get('action');

            switch ($action):
                case "add":

                    $check_exists = UserRole::where(['user_id' => $user_id, 'role_id' => $role_id])
                        ->first();

                    if(!$check_exists):
                        UserRole::insert([
                            'user_id' => $user_id,
                            'role_id' => $role_id,
                            'created_at' => date('Y-m-d H:i:s')
                        ]);
                    endif;


                    break;
                case "remove":
                    UserRole::where([
                        'user_id' => $user_id,
                        'role_id' => $role_id
                    ])->delete();

                    break;
            endswitch;

            DB::commit();
            return Response::json(['success' => true, 'message' => 'update success']);
        }catch(\Exception $e){
            DB::rollback();
            return Response::json(['success' => false, 'message' => 'can not change user']);
        }


    }

    /**
     * @author vanhs
     * @desc Luu thong tin quyen
     * @param Request $request
     * @return mixed
     */
    public function savePermission(Request $request){
        $can_view = Permission::isAllow(Permission::PERMISSION_VIEW_ROLE);
        if(!$can_view):
            return Response::json(['success' => false, 'message' => 'not permission']);
        endif;

        $role_id = $request->get('role_id');

        $role = Role::find($role_id);

        if(!$role):
            return Response::json(['success' => false, 'message' => 'role not found']);
        endif;

        $permission_data = [];
        $permission_params = $request->get('permission');
        if(!$permission_params) $permission_params = [];

        foreach($permission_params as $permission_param):
            $permission_data[] = [
                'role_id' =>  $role_id,
                'code' => $permission_param,
                'created_at' => date('Y-m-d H:i:s')
            ] ;
        endforeach;

        try{
            DB::beginTransaction();

            Permission::where(['role_id' => $request->get('role_id')])->delete();

            if(count($permission_data)):
                Permission::insert($permission_data);
            endif;

            DB::commit();

            return Response::json(['success' => true, 'message' => 'save success']);
        }catch(\Exception $e){
            DB::rollback();
            return Response::json(['success' => false, 'message' => 'save not success']);
        }
    }

    /**
     * @author vanhs
     * @desc Chinh sua thong tin nhom quyen
     * @param Request $request
     * @return Redirect
     */
    public function updateRole(Request $request){

        $can_view = Permission::isAllow(Permission::PERMISSION_VIEW_ROLE);
        if(!$can_view):
            return redirect('403');
        endif;

        $data = $request->all();
        $role_id = $data['role_id'];

        $role = Role::find($request->get('role_id'));

        if(!$role):
            return redirect('404');
        endif;

        Role::where([
            'id' => $role_id
        ])->update([
            'label' => $data['label'],
            'state' => $data['state'],
            'description' => $data['description'],
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        return redirect("setting/role/$role_id");
    }

    /**
     * @author vanhs
     * @desc Lay cac thong tin can thiet de hien thi trang chi tiet nhom
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|Redirect|\Illuminate\View\View
     */
    public function roleDetail(Request $request){

        $can_view = Permission::isAllow(Permission::PERMISSION_VIEW_ROLE);
        if(!$can_view):
            return redirect('403');;
        endif;

        $id = $request->route('id');

        $role = Role::find($id);

        if(!$role):
            return redirect('404');
        endif;

        $role_name = $role->label;

        #region -- danh sach cac user thuoc role nay
        $users_in_role_array = UserRole::select('user_id')->where([
            'role_id' => $id
        ])->get()->toArray();

        $users_ids_in_role = [];
        if($users_in_role_array):
            foreach($users_in_role_array as $k => $v):
                $users_ids_in_role[] = $v['user_id'];
            endforeach;
        endif;
        $users_ids_in_role[] = 0;

        $users_in_role = User::select('*')->where([
            'section' => User::SECTION_CRANE,
            'status' => User::STATUS_ACTIVE,
        ])->whereIn('id', $users_ids_in_role)->get()->toArray();

        #endregion

        #region -- danh sach cac user khong thuoc ve role nay --
        $users_not_in_role = User::select('*')->where([
            'section' => User::SECTION_CRANE,
            'status' => User::STATUS_ACTIVE,
        ])->whereNotIn('id', $users_ids_in_role)->get()->toArray();
        #endregion

        #region -- danh sach cac quyen thuoc role nay --
        $permissions_role = Permission::select('code')->where([
            'role_id' => $id
        ])->get()->toArray();

        $permissions_role_list = [];
        if($permissions_role):
            foreach($permissions_role as $key => $value):
                $permissions_role_list[] = $value['code'];
            endforeach;
        endif;
        #endregion

        $data = [
            'page_title' => sprintf("Chi tiết nhóm [%s]", $role_name),
            'role' => $role,
            'role_id' => $id,
            'users_not_in_role' => $users_not_in_role,
            'users_in_role' => $users_in_role,
            'permissions_role' => $permissions_role_list,
            'permissions' => Permission::$permissions
        ];
        return view('role_detail', $data);
    }

    /**
     * @author vanhs
     * @desc Tao moi mot nhom quyen
     * @param Request $request
     * @return mixed
     */
    public function addRole(Request $request){
        $data_insert = $request->all();
        $data_insert['created_at'] = date('Y-m-d H:i:s');

        unset($data_insert['_token']);

        $can_insert = Permission::isAllow(Permission::PERMISSION_CREATE_ROLE);
        if(!$can_insert):
            return Response::json(['success' => false, 'message' => 'not permission']);
        endif;

        $validator = Validator::make($data_insert, [
            'label' => 'required'
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return Response::json(array('success' => false, 'message' => implode('<br>', $errors) ));
        }

        Role::insert($data_insert);

        return Response::json(['success' => true, 'message' => 'insert success']);
    }

    /**
     * @author vanhs
     * @desc Xoa nhom quyen
     * @param Request $request
     * @return mixed
     */
    public function deleteRole(Request $request){
        $id = $request->get('id');

        $can_delete = Permission::isAllow(Permission::PERMISSION_DELETE_ROLE);
        if(!$can_delete):
            return Response::json(['success' => false, 'message' => 'not permission']);
        endif;

        try{
            DB::beginTransaction();

            Role::find($id)->delete();

            UserRole::where([
                'role_id' => $id
            ])->delete();

            Permission::where([
                'role_id' => $id
            ])->delete();

            DB::commit();

            return Response::json(['success' => true, 'message' => 'delete success']);
        }catch(\Exception $e){
            DB::rollback();
            return Response::json(['success' => false, 'message' => 'delete not success']);
        }

    }

    public function roles(){
        $role = new Role();
        $roles = $role->newQuery()->orderBy('created_at', 'desc')->get()->toArray();
        $data = [
            'page_title' => "Nhóm & phân quyền ",
            'roles' => $roles,
            'permissions' => Permission::$permissions
        ];
        return view('role', $data);
    }

    /**
     * @author vanhs
     * @desc Hien thi thong tin trang cau hinh chung he thong
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getList(Request $request){
        $data_inserted = [];

        $data_inserted_array = SystemConfig::all()->toArray();

        $data_inserted_array = (array)$data_inserted_array;
        foreach($data_inserted_array as $data_inserted_array_item):
            $data_inserted[$data_inserted_array_item['config_key']] = $data_inserted_array_item['config_value'];
        endforeach;

//        echo SystemConfig::getConfigValueByKey('website_name');

        $data = [
            'page_title' => "Cấu hình chung hệ thống ",
            'data' => SystemConfig::$system_config_data,
            'data_inserted' => $data_inserted,
            'save' => $request->get('save')
        ];
        return view('system_config', $data);
    }

    /**
     * @author vanhs
     * @desc Luu thong tin cau hinh chung he thong
     * @param Request $request
     * @return Redirect
     */
    public function update(Request $request){

        $data_send = $request->all();

        unset($data_send['_token']);

        $system_config = new SystemConfig();

        $data_insert = [];

        $cache_data = [];

        foreach($data_send as $key => $data_send_item):
            $config_value = $data_send_item ? $data_send_item : '';

            $data_insert[] = [
                'config_key' => $key,
                'config_value' => $config_value,
                'created_at' => date('Y-m-d H:i:s')
            ];

            $cache_data[$key] = $config_value;
        endforeach;

        $system_config->updateData($data_insert);

        Cache::forever(SystemConfig::CACHE_SYSTEM_CONFIG_KEY, $cache_data);

//        $cache = Cache::get('system_config');

        return redirect("setting?save=success");
    }
}
