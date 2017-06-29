@extends($layout)

@section('page_title')
    {{@$page_title}}
@endsection

@section('css_bottom')
    {{--<link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap-select.min.css') }}">--}}
@endsection

@section('widget')

@endsection

@section('js_bottom')
    @parent

{{--    <script type="text/javascript" src="{{ asset('js/bootstrap-select.min.js') }}"></script>--}}
    <script>
        $(document).ready(function(){
            //todo
        });
    </script>
@endsection

@section('content')

    <div id="order-detail-page" class="row">
        <div class="col-sm-8 col-xs-12">

            <div class="card">

                @include('partials/__breadcrumb',
                                [
                                    'urls' => [
                                        ['name' => 'Trang chủ', 'link' => url('home')],
                                        ['name' => 'Đơn hàng', 'link' => url('order')],
                                        ['name' => 'Đơn ' . $order->code, 'link' => null],
                                    ]
                                ]
                            )

                <div class="card-body">
                    <div role="tabpanel">
                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs" role="tablist">
                            <li role="presentation" class="active">
                                <a href="#home" aria-controls="home" role="tab" data-toggle="tab">Thông tin chung</a>
                            </li>
                            <li role="presentation" class="">
                                <a href="#order-history" aria-controls="home" role="tab" data-toggle="tab">Lịch sử</a>
                            </li>
                            <li role="presentation">
                                <a href="#order-transaction" aria-controls="tab" role="tab" data-toggle="tab">Phí & LS Giao dịch</a>
                            </li>
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content">

                            <div role="tabpanel" class="tab-pane" id="order-history">
                                <table class="table no-padding-leftright">
                                    <tbody>
                                    <?php $count = 0; ?>

                                    <?php
                                    foreach(App\Order::$timeListOrderDetail as $k => $v){
                                    $count++;
                                    if(empty($order->$k)){
                                        continue;
                                    }
                                    ?>

                                    @if($count == 1)

                                        <tr>
                                            <td width="30%" class="border-top-none">{{$v}}</td>
                                            <td class="border-top-none">{{ App\Util::formatDate($order->$k) }}</td>
                                        </tr>

                                    @else
                                        <tr>
                                            <td>{{$v}}</td>
                                            <td>{{ App\Util::formatDate($order->$k)  }}</td>
                                        </tr>
                                    @endif

                                    <?php } ?>

                                    </tbody>
                                </table>
                            </div>

                            <div role="tabpanel" class="tab-pane active" id="home">
                                <table class="table no-padding-leftright">
                                            <tbody>
                                            <tr>
                                                <td width="30%" class="border-top-none">Mã đơn: </td>
                                                <td class="border-top-none">
                                                    <h4 style="margin-top: 0;">{{$order->code}}</h4>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Trạng thái: </td>
                                                <td>
                                                    <h4 style="margin-top: 0;">{{ App\Order::getStatusTitle($order->status)  }}</h4>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Khách hàng: </td>
                                                <td>
                                                    <a href="{{ url('user/detail', $customer->id)  }}">{{$customer->email}}</a> ({{$customer->code}})
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Dịch vụ</td>
                                                <td>
                                                    @if(!empty($services))
                                                        @foreach($services as $service)
                                                            <form class="___form">
                                                                <input type="hidden" name="action" value="choose_service">
                                                                <input type="hidden" name="method" value="post">
                                                                <input type="hidden" name="service" value="{{$service['code']}}">
                                                                <input type="hidden" name="url" value="{{ url('order/' .$order_id. '/action')  }}">
                                                                <input type="hidden" name="response" value="order_detail">
                                                                <input type="hidden" name="_token" value="{{ csrf_token()  }}">

                                                                <label class="checkbox-inline">
                                                                    <input
                                                                            class="___btn-action"
                                                                            @if($service['checked']) checked @endif
                                                                            @if($service['is_default']) disabled @endif
                                                                            type="checkbox"
                                                                            value="{{$service['code']}}">{{$service['name']}}
                                                                </label>

                                                            </form>
                                                        @endforeach
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Acc mua</td>
                                                <td>

                                                    @if($permission['can_change_order_account_purchase_origin'])

                                                    <form class="___form">
                                                        <input type="hidden" name="action" value="account_purchase_origin">
                                                        <input type="hidden" name="method" value="post">
                                                        <input type="hidden" name="url" value="{{ url('order/' .$order_id. '/action')  }}">
                                                        <input type="hidden" name="_token" value="{{ csrf_token()  }}">
                                                        <input type="hidden" name="response" value="order_detail">

                                                        <select data-action="account_purchase_origin" class="form-control ___select-action">
                                                            <option value="">Chọn Acc mua hàng site gốc</option>

                                                            @if($user_origin_site)
                                                                @foreach($user_origin_site as $key => $val)
                                                                    <option data-site="{{$val->site}}" @if($val->username == $order->account_purchase_origin) selected @endif value="{{$val->username}}">{{$val->site}} - {{$val->username}}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>

                                                    </form>

                                                    @else
                                                        {{$order->account_purchase_origin}}
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Tỉ lệ đặt cọc (%)</td>
                                                <td>
                                                    @if($permission['can_change_order_deposit_percent'])
                                                        <form class="___form">
                                                            <input type="hidden" name="action" value="change_deposit">
                                                            <input type="hidden" name="method" value="post">
                                                            <input type="hidden" name="url" value="{{ url('order/' .$order_id. '/action')  }}">
                                                            <input type="hidden" name="_token" value="{{ csrf_token()  }}">
                                                            <input type="hidden" name="response" value="order_detail">

                                                            <input name="deposit" type="text" value="{{$order->deposit_percent}}">
                                                            <a href="javascript:void(0)" class="___btn-action">Lưu</a>
                                                        </form>
                                                    @else
                                                        {{$order->deposit_percent}}
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Tỉ giá</td>
                                                <td>{{ App\Util::formatNumber($order->exchange_rate) }} <sup>đ</sup></td>
                                            </tr>
                                            {{--<tr>--}}
                                                {{--<td>Người bán</td>--}}
                                                {{--<td>--}}
                                                    {{--<img src="{{ App\Order::getFavicon($order->site)  }}" width="16px" alt="">--}}
                                                    {{--<span>{{$order->seller_id}}</span>--}}
                                                {{--</td>--}}
                                            {{--</tr>--}}
                                            {{--<tr>--}}
                                                {{--<td>Wangwang</td>--}}
                                                {{--<td>--}}
                                                    {{--<!-- aliwangwang -->--}}
                                                    {{--<a style="padding: 0 45px;position: relative;" target="_blank"--}}
                                                       {{--href="http://www.taobao.com/webww/ww.php?ver=3&amp;touid={{ $order->wangwang  }}&amp;siteid=cntaobao&amp;status=1&amp;charset=utf-8">--}}
                                                        {{--<img style="position: absolute;left: 3px;top: -4px;" border="0"--}}
                                                             {{--src="http://amos.alicdn.com/realonline.aw?v=2&amp;uid={{ $order->wangwang  }}&amp;site=cntaobao&amp;s=1&amp;charset=utf-8"--}}
                                                             {{--title="Click vào đây để chat với người bán">--}}
                                                    {{--</a>--}}
                                                {{--</td>--}}
                                            {{--</tr>--}}
                                            {{--<tr>--}}
                                                {{--<td>Địa điểm bán</td>--}}
                                                {{--<td>--}}
                                                    {{--{{$order->location_sale}}--}}
                                                {{--</td>--}}
                                            {{--</tr>--}}
                                            <tr>
                                                <td>Hóa đơn gốc</td>
                                                <td>
                                                    <form class="___form" onsubmit="return false;">
                                                        <input type="hidden" name="action" value="insert_original_bill">
                                                        <input type="hidden" name="method" value="post">
                                                        <input type="hidden" name="url" value="{{ url('order/' .$order_id. '/action')  }}">
                                                        <input type="hidden" name="_token" value="{{ csrf_token()  }}">
                                                        <input type="hidden" name="response" value="order_detail">
                                                        <input type="text" name="original_bill">
                                                        <a href="javascript:void(0)" class="___btn-action">Lưu</a>
                                                    </form>

                                                    <ul style="margin: 0;padding: 0;list-style: none;" id="_original-bill-list">
                                                        @if(count($original_bill))
                                                            @foreach($original_bill as $key => $val)
                                                                <li class="_original-bill-list-item">
                                                                    <a style="color: #337ab7;" href="{{  App\Order::originalBillWithLink($order->site, $val->original_bill) }}" target="_blank">{{$val->original_bill}}</a>

                                                                    &nbsp;&nbsp;&nbsp;

                                                                    <form class="___form" style="display: inline;">
                                                                        <input type="hidden" name="action" value="remove_original_bill">
                                                                        <input type="hidden" name="method" value="post">
                                                                        <input type="hidden" name="url" value="{{ url('order/' .$order_id. '/action')  }}">
                                                                        <input type="hidden" name="_token" value="{{ csrf_token()  }}">
                                                                        <input type="hidden" name="response" value="order_detail">
                                                                        <input type="hidden" name="original_bill_delete" value="{{$val->original_bill}}">

                                                                        <a href="javascript:void(0)" class="___btn-action"><i class="fa fa-times"></i></a>
                                                                    </form>

                                                                </li>
                                                            @endforeach
                                                        @endif
                                                    </ul>



                                                </td>
                                            </tr>



                                            <tr>
                                                <td>Vận đơn</td>
                                                <td>

                                                    @if($permission['can_add_freight_bill_to_order'])
                                                    <form class="___form" onsubmit="return false;">
                                                        <input type="hidden" name="action" value="insert_freight_bill">
                                                        <input type="hidden" name="method" value="post">
                                                        <input type="hidden" name="url" value="{{ url('order/' .$order_id. '/action')  }}">
                                                        <input type="hidden" name="_token" value="{{ csrf_token()  }}">
                                                        <input type="hidden" name="response" value="order_detail">
                                                        <input placeholder="" type="text" name="freight_bill" value="">
                                                        <a href="javascript:void(0)" class="___btn-action">Lưu</a>
                                                    </form>
                                                    @endif

                                                    <ul style="margin: 0;padding: 0;list-style: none;" id="_freight-bill-list">
                                                        @if(count($freight_bill))
                                                            @foreach($freight_bill as $key => $val)
                                                                <li class="_freight-bill-list-item">


                                                                    @if($val->orders)
                                                                        <span class="text-danger">{{$val->freight_bill}}</span>
                                                                        &nbsp;&nbsp;&nbsp; Mã đã tồn tại ở đơn:
                                                                        @foreach($val->orders as $kkk => $o)
                                                                            <a href="{{ url('order/detail', $o->id) }}">{{$o->code}}</a>@if($kkk + 1 < count($val->orders)), @endif
                                                                        @endforeach
                                                                    @else
                                                                        {{$val->freight_bill}}
                                                                    @endif

                                                                    <form class="___form" style="display: inline;">
                                                                        <input type="hidden" name="action" value="remove_freight_bill">
                                                                        <input type="hidden" name="method" value="post">
                                                                        <input type="hidden" name="url" value="{{ url('order/' .$order_id. '/action')  }}">
                                                                        <input type="hidden" name="_token" value="{{ csrf_token()  }}">
                                                                        <input type="hidden" name="response" value="order_detail">
                                                                        <input placeholder="" type="hidden" name="freight_bill_delete" value="{{$val->freight_bill}}">
                                                                        <a href="javascript:void(0)" class="___btn-action"><i class="fa fa-times"></i></a>
                                                                    </form>

                                                                </li>
                                                            @endforeach
                                                        @endif
                                                    </ul>




                                                </td>
                                            </tr>

                                            <tr>
                                                <td>Phí VC nội địa TQ (¥)</td>
                                                <td>

                                                    {{--<div class="input-group">--}}
                                                        {{--<input type="text" class="form-control" placeholder="Input group" aria-describedby="basic-addon1" value="">--}}
                                                        {{--<span class="input-group-addon" id="basic-addon1" style="padding: 0;">--}}
                                                            {{--<button class="btn btn-danger" style="margin: 0;border-radius: 0;"><i class="fa fa-user" aria-hidden="true"></i></button>--}}
                                                        {{--</span>--}}
                                                    {{--</div>--}}

                                                    @if($permission['can_change_order_domestic_shipping_fee'])


                                                        <form class="___form">
                                                            <input type="hidden" name="action" value="domestic_shipping_china">
                                                            <input type="hidden" name="method" value="post">
                                                            <input type="hidden" name="url" value="{{ url('order/' .$order_id. '/action')  }}">
                                                            <input type="hidden" name="_token" value="{{ csrf_token()  }}">
                                                            <input type="hidden" name="response" value="order_detail">
                                                            <input placeholder="Đơn vị NDT" type="text" name="domestic_shipping_china" value="{{ $order->domestic_shipping_fee  }}">


                                                            <a href="javascript:void(0)" class="___btn-action">Lưu</a>
                                                        </form>

                                                    @else
                                                        {{ $order->domestic_shipping_fee  }}
                                                    @endif
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>Kho nhận hàng</td>
                                                <td>

                                                    <form class="___form">
                                                        <input type="hidden" name="action" value="receive_warehouse">
                                                        <input type="hidden" name="method" value="post">
                                                        <input type="hidden" name="url" value="{{ url('order/' .$order_id. '/action')  }}">
                                                        <input type="hidden" name="_token" value="{{ csrf_token()  }}">
                                                        <input type="hidden" name="response" value="order_detail">

                                                        <select class="form-control ___select-action">
                                                            <option value="">Chọn kho</option>

                                                            @if($warehouse_receive)
                                                                @foreach($warehouse_receive as $key => $val)
                                                                    <option @if($val->code == $order->receive_warehouse) selected @endif value="{{$val->code}}">[{{$val->alias}}] {{$val->name}} ({{$val->code}})</option>
                                                                @endforeach
                                                            @endif
                                                        </select>

                                                    </form>




                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Kho phân phối</td>
                                                <td>

                                                    <form class="___form">
                                                        <input type="hidden" name="action" value="destination_warehouse">
                                                        <input type="hidden" name="method" value="post">
                                                        <input type="hidden" name="url" value="{{ url('order/' .$order_id. '/action')  }}">
                                                        <input type="hidden" name="_token" value="{{ csrf_token()  }}">
                                                        <input type="hidden" name="response" value="order_detail">

                                                        <select class="form-control ___select-action">
                                                            <option value="">Chọn kho</option>

                                                            @if($warehouse_distribution)
                                                                @foreach($warehouse_distribution as $key => $val)
                                                                    <option @if($val->code == $order->destination_warehouse) selected @endif value="{{$val->code}}">[{{$val->alias}}] {{$val->name}} ({{$val->code}})</option>
                                                                @endforeach
                                                            @endif
                                                        </select>

                                                    </form>



                                                </td>
                                            </tr>
                                            @if($order->user_address_id)
                                            <tr>
                                                <td>Đ/C nhận hàng</td>
                                                <td>
                                                    <i class="fa fa-user"></i> {{$user_address->reciver_name}} - <i class="fa fa-phone"></i> {{$user_address->reciver_phone}}
                                                    <br>
                                                    <i class="fa fa-map-marker"></i> {{$user_address->detail}}, {{$user_address->district_label}}, {{$user_address->province_label}}<br>
                                                </td>
                                            </tr>
                                            @endif

                                            </tbody>
                                        </table>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="order-transaction">
                                <h4>Phí trên đơn</h4>

                                <ul class="order-fee">
                                    @foreach($order_fee as $order_fee_item)
                                    <li>
                                        <label for="">{{$order_fee_item['label']}}</label>
                                        <div>{{$order_fee_item['value']}} <sup>đ</sup></div>
                                    </li>
                                    @endforeach
                                </ul>


                                <h4>LS giao dịch</h4>
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Mã GD</th>
                                            <th>Loại</th>
                                            <th>Trạng thái</th>
                                            <th>Thời gian</th>
                                            <th>Giá trị</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    @foreach($transactions as $transaction)
                                        <?php

                                        $user = App\User::find($transaction->user_id);
                                        $order2 = App\Order::find($transaction->object_id);

                                        if(!$user) $user = new App\User();
                                        if(!$order2) $order2 = new App\Order();
                                        ?>
                                        <tr>


                                            <td>
                                                {{$transaction->transaction_code}}<br>
                                                <small class="" style="color: grey">{{$transaction->transaction_note}}</small>
                                            </td>
                                            <td>
                                                {{ App\UserTransaction::$transaction_type[$transaction->transaction_type]  }}
                                            </td>
                                            <td>


                                    <span class="@if($transaction->state == App\UserTransaction::STATE_COMPLETED) label label-success @endif">
                                {{ App\UserTransaction::$transaction_state[$transaction->state]  }}
                                    </span>
                                            </td>


                                            <td>{{ App\Util::formatDate($transaction->created_at)  }}</td>
                                            <td>
                                <span class="text-danger">
                                    {{ App\Util::formatNumber($transaction->amount) }} <sup>d</sup>
                                </span>
                                            </td>

                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>


                </div>
            </div>

            <br>

            @if($permission['can_view_package_list'])
                @if(!count($packages))

                @else
                <div class="card">
                    <div class="card-body">
                        <table class="table no-padding-leftright">
                            <thead>
                            <tr>
                                <th class="text-uppercase">Kiện hàng</th>
                                <th class="text-uppercase">Vận đơn</th>
                                <th class="text-uppercase">Trạng thái</th>
                                <th class="text-uppercase">Người tạo</th>
                                <th class="text-uppercase">Thời gian</th>
                            </tr>
                            </thead>
                            <tbody>

                                @foreach($packages as $package)
                                    <tr>
                                        <td>
                                            <a href="{{ url('package', $package->logistic_package_barcode)  }}" target="_blank">{{$package->logistic_package_barcode}}</a>

                                            @if($package->weight)
                                                <br>
                                                <small>
                                                    {{ $package->getWeightCalFee() }} kg
                                                </small>
                                            @endif
                                        </td>
                                        <td>
                                            {{$package->freight_bill}}
                                        </td>
                                        <td>
                                            <span class=" label label-success ">
                                                {{ App\Package::getStatusTitle($package->status)  }}
                                            </span>
                                            <br>

                                            <small>
                                                @if($package->current_warehouse)
                                                    Kho hiện tại: {{$package->current_warehouse}} <br>
                                                @endif

                                                @if($package->warehouse_status)
                                                    Tình trạng: {{ App\Package::getWarehouseStatusName($package->warehouse_status) }}
                                                    @if($package->warehouse_status == App\Package::WAREHOUSE_STATUS_IN)
                                                        ({{ App\Util::formatDate($package->warehouse_status_in_at)}})
                                                    @endif

                                                    @if($package->warehouse_status == App\Package::WAREHOUSE_STATUS_OUT)
                                                        ({{ App\Util::formatDate($package->warehouse_status_out_at)}})
                                                    @endif
                                                @endif

                                            </small>
                                        </td>
                                        <td>
                                            <?php
                                            $created_user = App\User::find($package->created_by);
                                            ?>
                                            <a href="{{ url('user/detail', $package->created_by)  }}" target="_blank">
                                                {{ $created_user->email  }}
                                            </a>
                                            <br>
                                            {{ $created_user->name  }}
                                        </td>
                                        <td>{{  App\Util::formatDate($package->created_at)}}</td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>

                <br>
                @endif
            @else
                <h5>Bạn không có quyền xem kiện hàng trên đơn!</h5>
            @endif


            <div class="card">

                <div class="card-body">
                    <?php
                    $total_order_quantity = 0;
                    $total_price_ndt = 0;
                    $total_price_vnd = 0;
                    if(count($order_items)){
                        foreach($order_items as $order_item){
                            $total_order_quantity += $order_item->order_quantity;
                            $total_price_ndt += $order_item->getPriceCalculator() * $order_item->order_quantity;
                            $total_price_vnd += $order_item->getPriceCalculator() * $order_item->order_quantity * $order->exchange_rate;
                        }
                    }

                    ?>



                    <table class="table table-striped no-padding-leftright">
                        <thead>
                        <tr>
                            <th width="50%">SẢN PHẨM</th>
                            <th width="20%">SL ({{ $total_order_quantity }})</th>
                            <th width="30%">{{ $total_price_ndt  }}¥ · {{ App\Util::formatNumber($total_price_vnd)  }} <sup>đ</sup></th>
                        </tr>
                        </thead>
                        @if(count($order_items))
                        <tbody>
                        @foreach($order_items as $order_item)
                            <tr>

                                <td>
                                    <div class="row">
                                        <div class="col-sm-2">
                                            <a href="{{$order_item->link}}" target="_blank">
                                                <img class="img-responsive" width="90px" src="{{$order_item->image}}" alt="">
                                            </a>
                                        </div>
                                        <div class="col-sm-10">
                                            ID: #{{$order_item->id}}<br>
                                            <a href="{{$order_item->link}}" target="_blank">Link gốc</a>
                                            <br>

                                            <p>
                                                Địa điểm đăng bán: {{$order_item->location_sale}}
                                            </p>
                                            <p>
                                                Mẫu: {{$order_item->property}}
                                            </p>

                                            <form class="___form">
                                                <input type="hidden" name="action" value="order_item_comment">
                                                <input type="hidden" name="method" value="post">
                                                <input type="hidden" name="item_id" value="{{$order_item->id}}">
                                                <input type="hidden" name="url" value="{{ url('order/' .$order_id. '/action')  }}">
                                                <input type="hidden" name="_token" value="{{ csrf_token()  }}">
                                                <input type="hidden" name="response" value="order_detail">

                                                <input style="width: 100%; margin-bottom: 10px;"
                                                       name="order_item_comment_message"
                                                       class="___input-action"
                                                       type="text"
                                                       data-key-global="order-item-comment-{{$order_item->id}}"
                                                       placeholder="Chat về sản phẩm...">

                                            </form>

                                            <ul style="    margin: 0;
    padding: 0;
    list-style: none;
    font-size: 13px;">
                                                @if(!empty($order_item_comments[$order_item->id]))
                                                    @foreach($order_item_comments[$order_item->id] as $order_item_comment)
                                                        <li style="margin-bottom: 5px;

                                                        @if(in_array($order_item_comment->type_context, [App\Comment::TYPE_CONTEXT_ACTIVITY, App\Comment::TYPE_CONTEXT_LOG]))

                                                                color: grey;
                                                        @endif

">
                                                            @if($order_item_comment->type_context != App\Comment::TYPE_CONTEXT_LOG)

                                                                <strong>{{$order_item_comment->user->name}}</strong>

                                                            @endif

                                                            {{$order_item_comment->message}}
                                                            <small>
                                                                {{ App\Util::formatDate($order_item_comment->created_at)  }}
                                                            </small>

                                                        </li>
                                                    @endforeach
                                                @endif

                                            </ul>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($permission['can_change_order_item_quantity'])

                                        <form class="___form">
                                            <input type="hidden" name="action" value="change_order_item_quantity">
                                            <input type="hidden" name="method" value="post">
                                            <input type="hidden" name="item_id" value="{{$order_item->id}}">
                                            <input type="hidden" name="url" value="{{ url('order/' .$order_id. '/action')  }}">
                                            <input type="hidden" name="_token" value="{{ csrf_token()  }}">
                                            <input type="hidden" name="response" value="order_detail">

                                            <input
                                                    style="width: 80px;"
                                                    class="___input-action"
                                                    name="order_quantity"
                                                    data-key-global="order-item-quantity-{{$order_item->id}}"
                                                    type="number"
                                                    value="{{$order_item->order_quantity}}" placeholder="">

                                        </form>


                                    @else
                                        {{$order_item->order_quantity}}
                                    @endif
                                </td>
                                <td>
                                    <div>
                                        Đơn giá:
                                        @if($permission['can_change_order_item_price'])


                                        <form class="___form" style="display: inline">
                                            <input type="hidden" name="action" value="change_order_item_price">
                                            <input type="hidden" name="method" value="post">
                                            <input type="hidden" name="item_id" value="{{$order_item->id}}">
                                            <input type="hidden" name="url" value="{{ url('order/' .$order_id. '/action')  }}">
                                            <input type="hidden" name="_token" value="{{ csrf_token()  }}">
                                            <input type="hidden" name="response" value="order_detail">

                                            <input
                                                    style="width: 90px;"
                                                    class="___input-action _autoNumeric"
                                                    type="text"
                                                    name="order_item_price"
                                                    data-key-global="order-item-price-{{$order_item->id}}"
                                                    value="{{ $order_item->getPriceCalculator() }}" placeholder="">¥ ·

                                        </form>


                                        @else
                                            <span class="text-success">{{ $order_item->getPriceCalculator() }}</span>¥ ·
                                        @endif
                                        {{ App\Util::formatNumber($order_item->getPriceCalculator() * $order->exchange_rate) }}

                                        <sup>đ</sup>
                                    </div>
                                    <div>
                                        Tổng: <span class="text-success">{{$order_item->getPriceCalculator() * $order_item->order_quantity}}¥</span> · {{ App\Util::formatNumber($order_item->getPriceCalculator() * $order_item->order_quantity * $order->exchange_rate) }} <sup>đ</sup>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                        @endif
                    </table>
                </div>
            </div>

            <br>

            <div class="dropdown">
                <button class="btn btn-danger dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                    Hành động
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                    @if($permission['can_change_order_bought'])
                        <li style="margin: 7px 0;">
                            <form class="___form">
                                <input type="hidden" name="action" value="bought_order">
                                <input type="hidden" name="method" value="post">
                                <input type="hidden" name="url" value="{{ url('order/' .$order_id. '/action')  }}">
                                <input type="hidden" name="_token" value="{{ csrf_token()  }}">
                                <input type="hidden" name="response" value="order_detail">

                                <a
                                        style="display: inline-block;width: 100%;padding: 0 15px;"
                                        href="javascript:void(0)"
                                        class="___btn-action">ĐÃ MUA</a>
                            </form>
                        </li>
                    @endif



                    @if($permission['can_change_order_received_from_seller'])
                        <li style="margin: 7px 0;">
                            <form class="___form">
                                <input type="hidden" name="action" value="received_from_seller_order">
                                <input type="hidden" name="method" value="post">
                                <input type="hidden" name="url" value="{{ url('order/' .$order_id. '/action')  }}">
                                <input type="hidden" name="_token" value="{{ csrf_token()  }}">
                                <input type="hidden" name="response" value="order_detail">

                                <a
                                        style="display: inline-block;width: 100%;padding: 0 15px;"
                                        href="javascript:void(0)"
                                        class="___btn-action">NHATMINH247 NHẬN</a>
                            </form>
                        </li>
                    @endif

                    @if($permission['can_change_order_cancel'])
                        <li style="margin: 7px 0;">
                            <form class="___form">
                                <input type="hidden" name="action" value="cancel_order">
                                <input type="hidden" name="method" value="post">
                                <input type="hidden" name="url" value="{{ url('order/' .$order_id. '/action')  }}">
                                <input type="hidden" name="_token" value="{{ csrf_token()  }}">
                                <input type="hidden" name="response" value="order_detail">

                                <a
                                        style="display: inline-block;width: 100%;padding: 0 15px;"
                                        href="javascript:void(0)"
                                        class="___btn-action">HỦY ĐƠN</a>
                            </form>
                        </li>
                    @endif

                </ul>
            </div>

            <br>

        </div>

        <div class="col-sm-4 col-xs-12" id="anchor-box-comment">
            @include('partials/__comment', [
                'object_id' => $order_id,
                'object_type' => App\Comment::TYPE_OBJECT_ORDER,
            ])

        </div>
    </div>


@endsection



