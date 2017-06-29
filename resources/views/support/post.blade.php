@extends('layouts/support')

@section('content')
    <!-- Page Heading -->
    <h1 class="my-4">{{ $post->post_title  }}</h1>

    @if($can_edit_post)
        <a href="{{ url('post', $post->id)  }}">Sửa bài viết</a>
    @endif

    <!-- Project Four -->
    <div class="row">
        <div class="col-md-12">
            <p>
                {{ $post->post_excerpt  }}
            </p>

            <br>
            {!! $post->post_content !!}
        </div>
    </div>
    <!-- /.row -->

    <hr>
@endsection

@section('sidebar')
    <div class="row">
        <div class="col-md-12">
            <h3 class="my-4">Bài viết gần đây</h3>
            <ul style="padding-left: 18px;">
                @foreach($posts_newest as $posts_newest_item)
                <li>
                    <a href="{{ url('ho-tro', $posts_newest_item->id)  }}" title="{{$posts_newest_item->post_title}}">
                        {{$posts_newest_item->post_title}}
                    </a>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
@endsection