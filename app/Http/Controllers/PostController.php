<?php

namespace App\Http\Controllers;

use App\Permission;
use App\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\User;

class PostController extends Controller
{

    protected $action_error = [];

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function createPost(Request $request){
        $post_id = $request->route('id');
        $post = Post::find($post_id);

        return view('post_add', [
            'post_id' => $post_id,
            'post' => $post,
            'page_title' => 'Tạo bài viết',
        ]);
    }

    public function previewPost(Request $request){
        $post_id = $request->route('id');

        $post = Post::find($post_id);

        if(!$post || !$post instanceof Post){
            return redirect('404');
        }

        $can_view = Permission::isAllow(Permission::PERMISSION_MANAGER_POST);
        if(!$can_view){
            return redirect('403');
        }

        $author = User::find($post->create_user_id);

        return view('post_preview', [
            'page_title' => 'Bài viết: ' . $post->post_title,
            'post' => $post,
            'author' => $author
        ]);
    }

    public function indexs(){
        $per_page = 30;
        $posts = Post::orderBy('id', 'desc');
        $total_posts = $posts->count();
        $posts = $posts->paginate($per_page);

        return view('post_list', [
            'page_title' => 'Quản lý bài viết',
            'posts' => $posts,
            'total_posts' => $total_posts,
        ]);
    }

    public function action(Request $request)
    {

        try{
            DB::beginTransaction();
            $current_user = User::find(Auth::user()->id);
            $action = '__' . $request->get('action');

            if(!$current_user || !$current_user instanceof User){
                return response()->json(['success' => false, 'message' => 'Current user not found!']);
            }

            if (!method_exists($this, $action)) {
                return response()->json(['success' => false, 'message' => 'Not support action!']);
            }

            $result = $this->$action($request, $current_user);
            if(!$result){
                return response()->json( ['success' => false, 'message' => implode('<br>', $this->action_error)] );
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'success',
                'redirect' => url('posts'),
            ]);

        }catch(\Exception $e){
            DB::rollback();
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra, vui lòng thử lại']);
        }

    }

    private function __post_add($request, $current_user){
        $can_view = Permission::isAllow(Permission::PERMISSION_MANAGER_POST);
        if(!$can_view){
            $this->action_error[] = 'Not permission!';
            return false;
        }

        if(empty($request->get('post_title'))){
            $this->action_error[] = 'Tiêu đề bài viết không để trống!';
        }

        if(count($this->action_error)){
            return false;
        }

        $post = new Post();
        $post_id = $request->get('post_id');
        if($post_id > 0){
            $post = Post::find($post_id);
        }

        $post->post_title = $request->get('post_title');
        $post->taxonomy_id = $request->get('taxonomy_id');
        $post->post_excerpt = $request->get('post_excerpt');
        $post->post_content = $request->get('summernote');
        $post->create_user_id = $current_user->id;
        $post->save();

        return $post;
    }
}
