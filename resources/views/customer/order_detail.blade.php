@extends($layout)

@section('page_title')
    {{@$page_title}}
@endsection

@section('content')
    <div id="order-detail-page" class="row">
        <div class="col-sm-8 col-xs-12">

            <div class="card">

                @include('partials/__breadcrumb',
                                [
                                    'urls' => [
                                        ['name' => 'Trang chủ', 'link' => url('home')],
                                        ['name' => 'Đơn hàng', 'link' => url('don-hang')],
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
                                                <td>Trạng thái</td>
                                                <td>
                                                    <h4 style="margin-top: 0;">{{ App\Order::getStatusTitle($order->status)  }}</h4>
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
                                                                <input type="hidden" name="url" value="{{ url('don-hang/' .$order_id. '/hanh-dong')  }}">
                                                                <input type="hidden" name="response" value="customer/order_detail">
                                                                <input type="hidden" name="_token" value="{{ csrf_token()  }}">

                                                                <label class="checkbox-inline">
                                                                    <input
                                                                            class="___btn-action"
                                                                            @if($service['checked']) checked @endif
                                                                            @if($service['is_default']) disabled @endif
                                                                            @if(!$permission['can_change_order_service']) disabled @endif
                                                                            type="checkbox"
                                                                            value="{{$service['code']}}">{{$service['name']}}
                                                                </label>

                                                            </form>
                                                        @endforeach
                                                    @endif
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>Tỉ lệ đặt cọc (%)</td>
                                                <td>
                                                    {{$order->deposit_percent}} ({{ App\Util::formatNumber($order->deposit_amount)}} <sup>đ</sup>)
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Tỉ giá</td>
                                                <td>{{ App\Util::formatNumber($order->exchange_rate) }} <sup>đ</sup></td>
                                            </tr>

                                            <tr>
                                                <td>Phí VC nội địa TQ (¥)</td>
                                                <td>
                                                    {{ $order->domestic_shipping_fee  }}
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>Kho phân phối</td>
                                                <td>
                                                    {{$order->destination_warehouse}}
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

            <div class="card">
                <div class="card-body">
                    <table class="table no-padding-leftright">
                        <thead>
                        <tr>
                            <th class="text-uppercase">Kiện hàng</th>
                            <th class="text-uppercase">Vận đơn</th>
                            <th class="text-uppercase">Trạng thái</th>
                            <th class="text-uppercase">Thời gian</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(!empty($packages))
                            @foreach($packages as $package)
                                <tr>
                                    <td>
                                        <code>{{$package->logistic_package_barcode}}</code>

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

                                    <td>{{  App\Util::formatDate($package->created_at)}}</td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <br>

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
                                                    <input type="hidden" name="url" value="{{ url('don-hang/' . $order_id . '/hanh-dong')  }}">
                                                    <input type="hidden" name="_token" value="{{ csrf_token()  }}">
                                                    <input type="hidden" name="response" value="customer/order_detail">

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
                                        {{$order_item->order_quantity}}
                                    </td>
                                    <td>
                                        <div>
                                            Đơn giá:
                                            <span class="text-success">{{ $order_item->getPriceCalculator() }}</span>¥ ·
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
                <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                    Hành động
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">

                    @if($permission['can_change_order_cancel'])
                        <li style="margin: 7px 0;">
                            <form class="___form">
                                <input type="hidden" name="action" value="cancel_order">
                                <input type="hidden" name="method" value="post">
                                <input type="hidden" name="url" value="{{ url('don-hang/' .$order_id. '/hanh-dong')  }}">
                                <input type="hidden" name="_token" value="{{ csrf_token()  }}">
                                <input type="hidden" name="response" value="customer/order_detail">

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
                'scope_view' => App\Comment::TYPE_EXTERNAL
            ])

        </div>
    </div>
@endsection

@section('js_bottom')
    @parent
    <script>
        $(document).ready(function(){
            //todo
        });

    </script>
@endsection

