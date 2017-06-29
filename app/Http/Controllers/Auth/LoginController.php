<?php

namespace App\Http\Controllers\Auth;

use App\Permission;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use App\User;
use App\Role;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }

    protected function credentials(Request $request){
        return array_merge($request->only($this->username(), 'password'), ['status' => User::STATUS_ACTIVE]);
    }


    protected function authenticated(Request $request, $user){
        $user_id = Auth::user()->id;

        $role_ids = [];
        $user_roles = User::find($user_id)->role;
        foreach($user_roles as $user_role):
            $role = Role::find($user_role->role_id);
            if($role && $role->state == Role::STATE_ACTIVE):
                $role_ids[$role->id] = $role->id;
            endif;
        endforeach;

        $role_ids = array_values($role_ids);
        $permission_code = [];
        if(count($role_ids)):
            $permissions = Permission::whereIn('role_id', $role_ids)->get();
            foreach($permissions as $permission):
                $permission_code[$permission->code] = $permission->code;
            endforeach;
        endif;

        $permission_code = array_values($permission_code);

        Cache::forever("user_permission_{$user_id}", $permission_code);
    }
}
