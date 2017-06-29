@extends('layouts.app')

@section('page_title')
    {{@$page_title}}
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                @include('partials/__breadcrumb',
                    [
                        'urls' => [
                            ['name' => 'Trang chủ', 'link' => url('home')],
                            ['name' => 'Quản lý bài viết', 'link' => url('posts')],
                            ['name' => 'Tạo bài viết', 'link' => null],
                        ]
                    ]
                )
                <div class="card-body">
                    <h3>{{@$page_title}}</h3>

                    <form class="___form">
                        <input type="hidden" name="action" value="post_add">
                        <input type="hidden" name="method" value="post">
                        <input type="hidden" name="url" value="{{ url('post/action')  }}">
                        <input type="hidden" name="_token" value="{{ csrf_token()  }}">

                        <input value="@if($post_id > 0){{$post->post_title}}@endif" autofocus type="text" class="form-control" name="post_title" placeholder="Tiêu đề bài viết...">
                        <textarea placeholder="Mô tả ngắn về bài viết..." class="form-control" name="post_excerpt" id="" rows="3" cols="80">@if($post_id > 0){{$post->post_excerpt}}@endif</textarea>
                        <input type="hidden" name="taxonomy_id" value="@if($post_id > 0){{$post->taxonomy_id}}@endif">
                        <input type="hidden" name="post_id" value="@if($post_id > 0){{$post->id}}@endif">

                        <div id="summernote">@if($post_id > 0){!! $post->post_content !!}@endif</div>
                        {{--<textarea class="form-control" name="post_content" id="editor11111" rows="10" cols="80">@if($post_id > 0){{$post->post_content}}@endif</textarea>--}}
                        <button class="btn btn-danger ___btn-action" type="button">LƯU</button>
                    </form>


                </div>
            </div>
        </div>
    </div>
@endsection

@section('css_bottom')
    @parent
    <link href="{{ asset('bower_components/summernote/dist/summernote.css')  }}" rel="stylesheet">
@endsection

@section('js_bottom')
    @parent

    <script src="{{ asset('bower_components/summernote/dist/summernote.js')  }}"></script>

    <script>
        $(document).ready(function(){
            $('#summernote').summernote({
                height: 300,
            });

            {{--$('#summernote').summernote('code', '@if($post_id > 0){{$post->post_content}}@endif');--}}
        });
    </script>
@endsection

