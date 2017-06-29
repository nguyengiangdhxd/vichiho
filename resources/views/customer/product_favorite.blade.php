@extends('layouts.app')
{{--@extends('layouts.app_blank')--}}

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
                                        ['name' => 'Trang chủ', 'link' => $app->make('url')->to('home')],
                                        ['name' => 'Sản phẩm đã lưu', 'link' => null],
                                    ]
                                ]
                            )

                <div class="card-body">

                    @if($total_product_favorite)

                        <table class="table table-striped table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>TT</th>
                                <th>Site</th>
                                <th>Sản phẩm</th>
                                <th>Thời gian lưu</th>
                            </tr>
                            </thead>
                            <tbody>


                                @foreach($product_favorite as $key => $product_favorite_item)
                                    <tr>
                                        <td>{{ $key + 1  }}</td>
                                        <td>{{$product_favorite_item->site}}</td>
                                        <td>
                                            <a href="{{$product_favorite_item->link}}">
                                                {{$product_favorite_item->product_name}}
                                            </a>
                                            <img style="
                                                    float: left;
                                                    margin-right: 10px;"
                                                 width="80px;"
                                                 src="{{$product_favorite_item->avatar}}" alt="">
                                        </td>
                                        <td>{{ App\Util::formatDate($product_favorite_item->updated_at)  }}</td>
                                    </tr>
                                @endforeach


                            </tbody>
                        </table>

                        {{ $product_favorite->links() }}

                    @else

                        <h3>Hiện tại bạn chưa lưu sản phẩm nào!</h3>

                    @endif


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

