@extends('layouts/app_blank')

@section('page_title')
    {{@$page_title}}
@endsection

<a href="{{ url('posts')  }}">>> Về trang quản lý bài viết</a>
<br>

@section('content')
    <h3>{{$post->post_title}}</h3>
    <p>
        Người tạo: <strong>{{$author->name}}</strong> ({{$author->email}})
    </p>
    <p>Tạo lúc: {{App\Util::formatDate($post->created_at)}}</p>

    <p>
        {{$post->post_excerpt}}
    </p>

    <hr>

    <div>
        {!! $post->post_content !!}
    </div>
@endsection