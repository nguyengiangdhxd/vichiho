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
                            ['name' => 'Thống kê tài chính', 'link' => null],
                        ]
                    ]
                )
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12 col-xs-12">
                            <h3>Khách hàng</h3>
                            <table class="table">
                                <thead>
                                <tr class="no-padding-leftright">
                                    <th>Thông tin khách</th>
                                    <th class="text-right">Tiền hàng</th>
                                    <th class="text-right">Thanh toán</th>
                                    <th class="text-right">Nợ lại</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($users as $user)
                                    <tr class="no-padding-leftright">
                                        <td>
{{--                                            <a href="{{ url('transaction/statistic?customer_id=' . $user->id)  }}">{{$user->email}}</a>--}}
                                            <a href="{{ url('user/detail', $user->id)  }}">{{$user->email}}</a>
                                        </td>
                                        <td class="text-danger text-right">
                                            {{App\Util::formatNumber($user->amount_vnd)}}đ
                                        </td>
                                        <td class="text-danger text-right">
                                            {{App\Util::formatNumber($user->payment_vnd)}}đ
                                        </td>
                                        <td class="text-danger text-right">
                                            {{App\Util::formatNumber($user->need_payment_vnd)}}đ
                                        </td>
                                    </tr>
                                @endforeach
                                <tr class="no-padding-leftright">
                                    <td><strong>Tổng tiền</strong></td>
                                    <td class="text-danger text-right"><strong>{{App\Util::formatNumber($total['total_amount_vnd'])}}đ</strong></td>
                                    <td class="text-danger text-right"><strong>{{App\Util::formatNumber($total['total_payment_vnd'])}}đ</strong></td>
                                    <td class="text-danger text-right"><strong>{{App\Util::formatNumber($total['total_need_payment_vnd'])}}đ</strong></td>
                                </tr>
                                </tbody>
                            </table>
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

