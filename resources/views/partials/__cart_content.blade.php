<div class="row">
    <div class="col-xs-12">
        @include('partials/__cart_step', ['active' => 1])
    </div>
</div>

<div class="row">
    <div class="col-xs-12">
        <strong>{{$data['statistic']['total_shops']}}</strong> shop / <strong>{{$data['statistic']['total_items']}}</strong> sản phẩm / <strong><span class="_autoNumeric">{{$data['statistic']['total_amount']}}</span></strong>đ tiền hàng
    </div>
</div>

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
                            <input type="hidden" name="url" value="{{ url('cart/shop') }}">
                            <input type="hidden" name="confirm" value="Bạn có chắc muốn xoá shop này?">
                            <input type="hidden" name="response" value="partials/__cart_content">
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

                        {{$shop->shop_name}}

                        <div style="position: absolute;
        top: 18px;
        right: 20px;">
                            @foreach($data['services'] as $service)
                                <div class="checkbox checkbox-inline">
                                    <input
                                            @if(in_array($service['code'], $shop->services)) checked @endif
                                    type="checkbox"
                                            value="{{$service['code']}}"
                                            class="_chk-service"
                                            data-shop-id="{{$shop->shop_id}}"
                                            id="checkbox_{{$service['code']}}_{{$shop->id}}">

                                    <label for="checkbox_{{$service['code']}}_{{$shop->id}}">
                                        {{$service['title']}}
                                    </label>
                                </div>
                            @endforeach


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
                                                <input type="hidden" name="url" value="{{ url('cart/item') }}">
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
                                            <img style="margin-right: 10px;" src="{{ urldecode($item->image_model) }}" class="pull-left" width="50px" />
                                            <a href="{{$item->link_origin}}" target="_blank">
                                                {{$item->title_origin}}
                                            </a>
                                            <br />
                                            <small>{{$item->property}}</small>
                                            <br>
                                            <input data-shop-id="{{$shop->shop_id}}" data-item-id="{{$item->id}}" placeholder="Ghi chú sản phẩm..." style="width: 250px; padding: 0 5px;" type="text" class="_comment" value="{{$item->comment}}" />

                                        </td>

                                        <td><span class="_autoNumeric">{{$item->price_calculator_vnd}}</span>đ / ¥{{$item->price_calculator}}</td>
                                        <td>
                                            <input
                                                    data-shop-id="{{$shop->shop_id}}"
                                                    data-item-id="{{$item->id}}" style="width: 80px"
                                                    type="number"
                                                    name="quantity" class="form-control text-center _quantity" value="{{$item->quantity}}" />
                                        </td>
                                        <td><span class="_autoNumeric">{{$item->total_amount_item_vnd}}</span>đ / ¥{{$item->total_amount_item}}</td>

                                    </tr>

                                @endforeach
                                <tr>
                                    <td class="text-right" colspan="5">
                                        Tổng tiền hàng: <span class="_autoNumeric">{{$shop->total_amount_items}}</span>đ

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
                <div class="card-body">
                    <h4>Giỏ hàng hiện đang trống!</h4>

                    Click vào <a target="_blank" href="https://nhatminh247.wordpress.com/2017/03/24/huong-dan-dat-coc-don-hang/">đây</a> để được huớng dẫn đặt hàng một cách chi tiết nhất!
                </div>
            </div>
        </div>

    </div>
@endif