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
                                        ['name' => 'Trang chủ', 'link' => url('home')],
                                        ['name' => 'Nhân viên', 'link' => url('user')],
                                        ['name' => $user->code, 'link' => null],
                                    ]
                                ]
                            )

                <div class="card-body">


                    <div role="tabpanel">
                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs" role="tablist">
                            <li role="presentation" class="active">
                                <a href="#userInfo" aria-controls="home" role="tab" data-toggle="tab"><h4>Thông tin nhân viên</h4></a>
                            </li>
                            <li role="presentation">
                                <a href="#userTransaction" aria-controls="tab" role="tab" data-toggle="tab"><h4>Lịch sử giao dịch</h4></a>
                            </li>
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="userInfo">



                                <div class="row">
                                    <div class="col-sm-4 col-xs-12">
                                        <h4>Thông tin chung &nbsp;&nbsp;&nbsp;<small><a href="{{ url('user/edit', $user_id)  }}">Sửa</a></small></h4>

                                        <table class="table">

                                            <tbody>
                                            <tr>
                                                <td class="no-padding-leftright"><strong>Họ & tên</strong>: {{$user->name}}</td>
                                            </tr>
                                            <tr>
                                                <td class="no-padding-leftright"><strong>Mã</strong>: {{$user->code}}</td>
                                            </tr>
                                            @if($user->section == App\User::SECTION_CUSTOMER)
                                                <tr>
                                                    <td class="no-padding-leftright"><strong>Số dư</strong>: {{ App\Util::formatNumber($user->account_balance)  }} <sup>đ</sup></td>
                                                </tr>
                                            @endif
                                            <tr>
                                                <td class="no-padding-leftright"><strong>Email</strong>: {{$user->email}}</td>
                                            </tr>
                                            <tr>
                                                <td class="no-padding-leftright"><strong>Đối tượng</strong>: {{ App\User::getSectionName($user->section)  }}</td>
                                            </tr>

                                            <tr>
                                                <td class="no-padding-leftright"><strong>Trạng thái</strong>: {{ App\User::getStatusName($user->status) }}</td>
                                            </tr>
                                            <tr>
                                                <td class="no-padding-leftright"><strong>Gia nhập</strong>: {{ App\Util::formatDate($user->created_at)  }}</td>
                                            </tr>
                                            <tr>
                                                <td class="no-padding-leftright"><strong>Cập nhật</strong>: {{ App\Util::formatDate($user->updated_at)  }}</td>
                                            </tr>

                                            </tbody>
                                        </table>


                                    </div>

                                    <div class="col-sm-4 col-xs-12">
                                        <h4>Điện thoại</h4>

                                        @if(!empty($user_mobiles))
                                            <ul id="_list-user-phone">
                                                @foreach($user_mobiles as $user_mobile)
                                                    <li class="_row-user-phone">
                                                        {{$user_mobile->mobile}}

                                                        &nbsp;&nbsp;<small style="color: grey">{{ App\Util::formatDate($user_mobile->created_at)  }}</small>

                                                        &nbsp;&nbsp;&nbsp;&nbsp;
                                                        @if($permission['can_remove_mobile'])
                                                        <a data-phone="{{$user_mobile->mobile}}"
                                                                data-id="{{ $user_mobile->id }}" href="javascript:void(0)" class="_remove-user-phone">

                                                            <i class="fa fa-times"></i>

                                                        </a>
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif

                                        <input type="text" class="_input-user-phone" autofocus placeholder="Nhập điện thoại...">

                                        @if($permission['can_add_mobile'])
                                            <a href="javascript:void(0)" class="_add-user-phone"><i class="fa fa-plus"></i> Thêm</a>
                                        @endif
                                    </div>
                                </div>

                            </div>
                            <div role="tabpanel" class="tab-pane" id="userTransaction">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Mã GD</th>
                                        <th>Loại</th>
                                        <th>Trạng thái</th>
                                        <th>Đơn</th>
                                        <th>Thời gian</th>
                                        <th>Giá trị</th>
                                        <th>Số dư cuối</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    @foreach($transactions as $transaction)
                                        <?php
                                        $user = App\User::find($transaction->user_id);
                                        $order = App\Order::find($transaction->object_id);

                                        if(!$user):
                                            $user = new App\User();
                                        endif;

                                        if(!$order):
                                            $order = new App\Order();
                                        endif;

                                        ?>
                                        <tr>
                                            <td>
                                                {{$transaction->id}}
                                            </td>

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
                                            <td>
                                                @if($transaction->object_type == App\UserTransaction::OBJECT_TYPE_ORDER)
                                                    <a href="{{ url('order', $order->id)  }}">{{$order->code}}</a>
                                                @endif
                                            </td>

                                            <td>{{ App\Util::formatDate($transaction->created_at)  }}</td>
                                            <td>
                                <span class="text-danger">
                                    {{ App\Util::formatNumber($transaction->amount)  }} <sup>d</sup>
                                </span>
                                            </td>
                                            <td>
                                                <strong>
                                                    {{ App\Util::formatNumber($transaction->ending_balance)  }} <sup>d</sup>
                                                </strong>
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
        </div>


    </div>
@endsection

@section('js_bottom')
    @parent
    <script>
        $(document).ready(function(){

            $(document).on('click', '._remove-user-phone', function(){
                 var user_phone = $(this).data('phone');
                 var user_phone_id = $(this).data('id');

                 var $that = $(this);

                $.ajax({
                    url: "{{ url('user/phone') }}",
                    method: 'put',
                    data: {
                        user_phone:user_phone,
                        user_phone_id:user_phone_id,
                        user_id: "{{$user_id}}",
                        _token: "{{csrf_token()}}"
                    },
                    success:function(response) {
                        if(response.success){
                            $that.parents('._row-user-phone').remove();
                        }else{
                            bootbox.alert(response.message);
                        }
                    },
                    error: function(){


                    }
                });
            });

            $(document).on('click', '._add-user-phone', function(){
                var user_phone = $('._input-user-phone').val();

                $.ajax({
                  url: "{{ url('user/phone')  }}",
                  method: 'post',
                  data: {
                      user_phone:user_phone,
                      user_id: "{{$user_id}}",
                      _token: "{{csrf_token()}}"
                  },
                  success:function(response) {
                    if(response.success){
                        window.location.reload();
                    }else{
                        $('._input-user-phone').focus();
                        bootbox.alert(response.message);
                    }
                  },
                  error: function(){

                  }
                });
            });
        });
    </script>
@endsection