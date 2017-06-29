@extends($layout)

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
                            ['name' => 'Kiện hàng', 'link' => url('packages')],
                            ['name' => 'Thông tin kiện hàng ' . $package_code, 'link' => null],
                        ]
                    ]
                )
                <div class="card-body">

                    <div class="row">


                        <div class="col-sm-8 col-xs-12">
                            <h3>
                                Kiện hàng #{{$package->logistic_package_barcode}}
                                <small>{{ App\Package::getStatusTitle($package->status)  }}</small>
                            </h3>

                            <br>

                            <table class="table no-padding-leftright">
                                <tbody>
                                    <tr>
                                        <td width="40%">Vận đơn: </td>
                                        <td>{{ $package->freight_bill }}</td>
                                    </tr>
                                    <tr>
                                        <td>Đơn hàng: </td>
                                        <td>
                                            @if($package->order)
                                                <a href="{{ url('order/detail', $package->order->id)  }}">{{ $package->order->code }}</a>
                                            @endif

                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Thông tin nhận hàng: </td>
                                        <td>
                                            @if($package->customer_address)
                                                <i class="fa fa-user"></i> {{$package->customer_address->reciver_name}} - <i class="fa fa-phone"></i> {{$package->customer_address->reciver_phone}}
                                                <i class="fa fa-map-marker"></i> {{$package->customer_address->detail}}, {{$package->customer_address->district_label}}, {{$package->customer_address->province_label}}
                                            @endif

                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Cân nặng tính phí: </td>
                                        <td>
                                            @if($package->weight_type == 1)
                                                {{$package->weight}} kg
                                            @endif

                                            @if($package->weight_type == 2)
                                                {{$package->converted_weight}} kg (cân nặng quy đổi)
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Ghi chú: </td>
                                        <td>{{ $package->note  }}</td>
                                    </tr>
                                    <tr>
                                        <td>Kho hiện tại: </td>
                                        <td>
                                            {{$package->current_warehouse}} - {{$package->warehouse_status}}

                                            @if($package->warehouse_status == App\Package::WAREHOUSE_STATUS_IN)
                                                (Nhập kho: {{  App\Util::formatDate($package->warehouse_status_in_at) }})
                                            @endif

                                            @if($package->warehouse_status == App\Package::WAREHOUSE_STATUS_IN)
                                                (Xuất kho: {{  App\Util::formatDate($package->warehouse_status_out_at) }})
                                            @endif
                                        </td>
                                    </tr>

                                <tr>
                                    <td>Thời gian: </td>
                                    <td>

                                        @foreach(App\Package::$timeListOrderDetail as $key => $value)
                                            @if($package->$key)
                                                {{$value}}: {{ App\Util::formatDate($package->$key) }}<br>
                                            @endif
                                        @endforeach
                                    </td>
                                </tr>

                                </tbody>
                            </table>

                            <div class="dropdown">
                                <button class="btn btn-danger dropdown-toggle" type="button" data-toggle="dropdown">Hành động
                                    <span class="caret"></span></button>
                                <ul class="dropdown-menu">
                                    <li><a href="">Sửa</a></li>
                                    <li><a href="">Xóa</a></li>
                                </ul>
                            </div>

                        </div>

                        <div class="col-sm-4 col-xs-12">
                            @if($package->order)
                                <h3>
                                    Đơn hàng: <a href="{{ url('order/detail', $package->order->id)  }}" target="_blank">{{ $package->order->code }}</a>
                                </h3>

                                <br>

                                @if($packages_order)
                                    <div class="list-group">
                                        @foreach($packages_order as $packages_order_item)
                                            <a href="{{ url('package', $packages_order_item->logistic_package_barcode)  }}" class="list-group-item

                                            @if($packages_order_item->logistic_package_barcode == $package->logistic_package_barcode)
                                                    active
                                            @endif

                                                    ">{{$packages_order_item->logistic_package_barcode}}</a>
                                        @endforeach
                                    </div>
                                @endif
                            @else
                                &nbsp;&nbsp;&nbsp;
                            @endif
                        </div>
                    </div>

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

