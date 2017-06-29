@extends('layouts.app')

@section('page_title')
    {{$page_title}}
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                @include('partials/__breadcrumb',
                    [
                        'urls' => [
                            ['name' => 'Trang chủ', 'link' => url('home')],
                            ['name' => 'Danh mục bài viết', 'link' => url('taxonomies')],
                            ['name' => 'Tạo danh mục', 'link' => null],
                        ]
                    ]
                )
                <div class="card-body">
                    Chức năng này hiện đang dược xây dựng!
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

