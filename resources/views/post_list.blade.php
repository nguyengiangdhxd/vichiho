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
                            ['name' => 'Quản lý bài viết', 'link' => null],
                        ]
                    ]
                )
                <div class="card-body">
                    <h3>{{@$page_title}}</h3>


                    <a class="btn btn-danger pull-right" href="{{ url('post')  }}">Tạo bài mới</a>

                    <p>Tìm thấy {{$total_posts}} bài viết</p>
                    <table class="table">
                        <thead>
                            <tr>
                                <th width="5%">ID</th>
                                <th width="85%">Bài viết</th>
                                <th width="10%">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>

                        @if(count($total_posts))
                            @foreach($posts as $post)

                                <?php
                                $author = App\User::find($post->create_user_id);
                                $post->author_email = $author->email;
                                $post->author_name = $author->name;

                                ?>
                                <tr>
                                    <td>{{$post->id}}</td>
                                    <td>
                                        <h4>
                                            <a href="{{ url('post/preview', $post->id)  }}" target="_blank">
                                                {{$post->post_title}}
                                            </a>
                                        </h4>

                                        <p>
                                            Đăng lúc: {{ App\Util::formatDate($post->created_at)  }}
                                        </p>
                                        <p>
                                            Người đăng: {{$post->author_name}} ({{$post->author_email}})
                                        </p>
                                        <br><br>
                                        <small>
                                            {{$post->post_excerpt}}
                                        </small>
                                    </td>

                                    <td>
                                        <a href="{{ url('post', $post->id)  }}">Sửa</a>
                                    </td>
                                </tr>
                            @endforeach
                        @endif

                        </tbody>
                    </table>

                    {{ $posts->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js_bottom')
    @parent
    <script>
        $(document).ready(function(){

        });
    </script>
@endsection

