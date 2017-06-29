<?php

namespace App\Http\Controllers\Support;

use App\Permission;
use App\Post;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function index(Request $request){
        $post_id = $request->route('id');
        $can_edit_post = false;

        if(Auth::check()){
            $can_edit_post = Permission::isAllow(Permission::PERMISSION_MANAGER_POST);
        }


        $post = Post::find($post_id);
        if(!$post || !$post instanceof Post){
            return redirect('404');
        }

        $author = User::find($post->create_user_id);
        if(!$author || !$author instanceof User){
            return redirect('404');
        }

        $posts_newest = Post::orderBy('id', 'desc')->get();

        return view('support/post', [
            'page_title' => 'Bài viết',
            'posts_newest' => $posts_newest,
            'post' => $post,
            'author' => $author,
            'can_edit_post' => $can_edit_post,
        ]);
    }
}
