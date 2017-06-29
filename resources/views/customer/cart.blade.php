@extends($layout)

@section('page_title')
    {{@$page_title}}
@endsection

@section('content')

    <div class="row">
        <div class="col-xs-12">
            @include('partials/__cart_step', ['active' => 1])
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <div class="pull-left">
                <strong>{{$data['statistic']['total_shops']}}</strong> shop / <strong>{{$data['statistic']['total_items']}}</strong> sản phẩm / <strong><span class="">{{ App\Util::formatNumber($data['statistic']['total_amount'])  }}</span></strong>đ tiền hàng
            </div>

            {{--<a href="{{ url('dat-coc?shop_id=' . implode(',', $data['shop_ids']))  }}" class="btn btn-danger pull-right text-uppercase">--}}
                {{--Đặt cọc {{$data['statistic']['total_shops']}} shop--}}
            {{--</a>--}}
        </div>
    </div>

    <br>

    @if(!empty($data['shops']))
        @foreach($data['shops'] as $shop)

        <div class="row _shop" data-shop-id="{{$shop->shop_id}}">
            <div class="col-xs-12">
                <div class="card ">
                    <div class="card-header" style="position: relative">

                        <form class="___form">
                            <input type="hidden" name="action" value="remove_shop">
                            <input type="hidden" name="method" value="post">
                            <input type="hidden" name="shop_id" value="{{$shop->shop_id}}">
                            <input type="hidden" name="url" value="{{ url('gio-hang/hanh-dong') }}">
                            <input type="hidden" name="confirm" value="Bạn có chắc muốn xoá shop này?">
                            <input type="hidden" name="response" value="customer/cart">
                            <input type="hidden" name="_token" value="{{ csrf_token()  }}">

                            <a href="javascript:void(0)"
                               class="___btn-action"
                               data-toggle="tooltip"
                               title="Xoá shop">
                                <i class="fa fa-trash-o"></i>
                            </a>
                        </form>

                        @if($shop->site == 'tmall')
                        <span class="label label-danger">tmall</span>&nbsp;
                        @endif

                        @if($shop->site == '1688')
                            <span class="label label-success">1688</span>&nbsp;
                        @endif

                        @if($shop->site == 'taobao')
                            <span class="label label-warning">taobao</span>&nbsp;
                        @endif

                        @if($shop->shop_name)
                            {{$shop->shop_name}}
                        @elseif($shop->shop_id)
                            {{$shop->shop_id}}
                        @endif

                        <div style="position: absolute;top: 18px;right: 20px;">
                            {{--@foreach($data['services'] as $service)--}}
                                {{--<div class="checkbox-inline">--}}

                                    {{--<form class="___form">--}}
                                        {{--<input type="hidden" name="action" value="choose_service">--}}
                                        {{--<input type="hidden" name="method" value="post">--}}
                                        {{--<input type="hidden" name="shop_id" value="{{$shop->shop_id}}">--}}
                                        {{--<input type="hidden" name="url" value="{{ url('gio-hang/hanh-dong') }}">--}}
                                        {{--<input type="hidden" name="response" value="customer/cart">--}}
                                        {{--<input type="hidden" name="_token" value="{{ csrf_token()  }}">--}}
                                        {{--<input type="hidden" name="service" value="{{$service['code']}}">--}}

                                        {{--<input--}}
                                                {{--@if(in_array($service['code'], $shop->services)) checked @endif--}}
                                                {{--type="checkbox"--}}
                                                {{--value="{{$service['code']}}"--}}
                                                {{--class="___btn-action"--}}
                                                {{--id="checkbox_{{$service['code']}}_{{$shop->id}}">--}}

                                    {{--</form>--}}


                                    {{--<label for="checkbox_{{$service['code']}}_{{$shop->id}}">--}}
                                        {{--{{$service['title']}}--}}
                                    {{--</label>--}}
                                {{--</div>--}}
                            {{--@endforeach--}}
                        </div>
                    </div>
                    <div class="card-body no-padding">
                        <div class="table-responsive">
                            <table class="table card-table">
                                <thead>
                                <tr>
                                    <th width="5%"></th>
                                    <th width="50%">Sản phẩm </th>
                                    <th width="15%">Đơn giá </th>
                                    <th width="15%" class="">SL</th>
                                    <th width="15%">Tiền hàng </th>

                                </tr>
                                </thead>
                                <tbody>

                                @foreach($shop->items as $item)
                                <tr class="_shop-item" data-shop-id="{{$shop->shop_id}}" data-shop-item-id="{{$item->id}}">
                                    <td>

                                        <form class="___form">
                                            <input type="hidden" name="action" value="remove_item">
                                            <input type="hidden" name="method" value="post">
                                            <input type="hidden" name="shop_id" value="{{$shop->shop_id}}">
                                            <input type="hidden" name="item_id" value="{{$item->id}}">
                                            <input type="hidden" name="url" value="{{ url('gio-hang/hanh-dong') }}">
                                            <input type="hidden" name="response" value="customer/cart">
                                            <input type="hidden" name="_token" value="{{ csrf_token()  }}">

                                            <a class="___btn-action"
                                               href="javascript:void(0)"
                                               data-toggle="tooltip"
                                               title="Xoá sản phẩm">
                                                <i class="fa fa-trash-o"></i>
                                            </a>
                                        </form>

                                    </td>

                                    <td>
                                        <img style="margin-right: 10px;"
                                             src="{{ urldecode($item->image_model) }}"
                                             class="pull-left"
                                             width="50px" />
                                        <a href="{{$item->link_origin}}" target="_blank">
                                            {{$item->title_origin}}
                                        </a>
                                        <br />
                                        <small>{{$item->property}}</small>
                                        <br>

                                        <form class="___form">
                                            <input type="hidden" name="action" value="comment">
                                            <input type="hidden" name="method" value="post">
                                            <input type="hidden" name="shop_id" value="{{$shop->shop_id}}">
                                            <input type="hidden" name="item_id" value="{{$item->id}}">
                                            <input type="hidden" name="url" value="{{ url('gio-hang/hanh-dong') }}">
                                            <input type="hidden" name="_token" value="{{ csrf_token()  }}">
                                            <input type="hidden" name="response" value="customer/cart">

                                            <input
                                                    data-shop-id="{{$shop->shop_id}}"
                                                    data-item-id="{{$item->id}}"
                                                    placeholder="Ghi chú sản phẩm..."
                                                    style="width: 250px; padding: 0 5px;"
                                                    name="comment"
                                                    type="text"
                                                    data-key-global="cart-item-comment-{{$shop->shop_id}}-{{$item->id}}"
                                                    class="___input-action" value="{{$item->comment}}" />

                                        </form>
                                    </td>

                                    <td><span class="">{{ App\Util::formatNumber($item->price_calculator_vnd)  }}</span>đ / ¥{{$item->price_calculator}}</td>
                                    <td>

                                        <form class="___form">
                                            <input type="hidden" name="action" value="update_quantity">
                                            <input type="hidden" name="method" value="post">
                                            <input type="hidden" name="shop_id" value="{{$shop->shop_id}}">
                                            <input type="hidden" name="item_id" value="{{$item->id}}">
                                            <input type="hidden" name="url" value="{{ url('gio-hang/hanh-dong') }}">
                                            <input type="hidden" name="_token" value="{{ csrf_token()  }}">
                                            <input type="hidden" name="response" value="customer/cart">

                                            <input
                                                    style="width: 80px"
                                                    type="number"
                                                    data-key-global="cart-item-quantity-{{$shop->shop_id}}-{{$item->id}}"
                                                    name="quantity" class="form-control text-center ___input-action" value="{{$item->quantity}}" />
                                        </form>

                                    </td>
                                    <td>
                                        <span class="">{{ App\Util::formatNumber($item->total_amount_item_vnd)  }}</span>đ / ¥{{$item->total_amount_item}}
                                    </td>

                                </tr>

                                @endforeach
                                <tr>
                                    <td class="text-right" colspan="5">
                                        Tổng tiền hàng: <span class="">{{ App\Util::formatNumber($shop->total_amount_items)  }}</span>đ

                                        <a href="{{ url('dat-coc?shop_id=' . $shop->shop_id)  }}" class="btn btn-danger text-uppercase">Đặt cọc</a>
                                    </td>
                                </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @endforeach
    @else
        <div class="row">
            <div class="col-sm-12">
                <div class="card ">
                    <div class="card-body text-center">
                        <h3>Giỏ hàng hiện đang trống!</h3>

                        {{--Click vào <a href="">đây</a> để được huớng dẫn đặt hàng một cách chi tiết nhất!--}}
                    </div>
                </div>
            </div>
        </div>
    @endif

@endsection

@section('js_bottom')
    @parent
    <script type="text/javascript" src="{{ asset('js/jquery.lazy.min.js') }}"></script>
    <script>
        $(function() {
            $('.lazy').lazy();
        });
    </script>
@endsection

