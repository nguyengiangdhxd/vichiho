<?php

namespace App\Http\Controllers;

use App\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use App\User;
use Illuminate\Support\Facades\View;

class CommentController extends Controller
{
    protected $action_error = [];

    public function __construct()
    {
        $this->middleware('auth');

    }

    public function action(Request $request)
    {
        try{
            DB::beginTransaction();

            $user = User::find(Auth::user()->id);
            $action = '__' . $request->get('action');

            if(!$user || !$user instanceof User){
                return response()->json(['success' => false, 'message' => 'User not found!']);
            }

            if(empty($request->get('object_id'))
                && empty($request->get('object_type'))
                && empty($request->get('scope_view'))){
                return response()->json(['success' => false, 'message' => 'Params is missing!']);
            }

            if (!method_exists($this, $action)) {
                return response()->json(['success' => false, 'message' => 'Not support action!']);
            }

            $result = $this->$action($request, $user);
            if(!$result){
                return response()->json( ['success' => false, 'message' => implode('<br>', $this->action_error)] );
            }

            DB::commit();

            $view = View::make($request->get('response'), [
                'object_id' => $request->get('object_id'),
                'object_type' => $request->get('object_type'),
                'scope_view' => $request->get('scope_view'),
            ]);
            $html = $view->render();

            return response()->json([
                'success' => true,
                'message' => 'success',
                'html' => $html,
                'anchor' => $request->get('anchor'),
            ]);

        }catch(\Exception $e){
            DB::rollback();
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra, vui lòng thử lại']);
        }

    }

    /**
     * @author vanhs
     * @desc Them comment
     * @param Request $request
     * @return mixed
     */
    private function __comment(Request $request, $user){
        if(empty($request->get('message'))){
            $this->action_error[] = 'Nội dung không để trống!';
        }

        if(count($this->action_error)){
            return false;
        }

        $comment = new Comment();
        $comment->user_id = $user->id;
        $comment->object_id = $request->get('object_id');
        $comment->object_type = $request->get('object_type');
        $comment->scope = $request->get('scope');
        $comment->message = $request->get('message');
        $comment->type_context = Comment::TYPE_CONTEXT_CHAT;
        $comment->is_public_profile = $request->get('is_public_profile');
        $comment->save();

        return true;
    }
}
