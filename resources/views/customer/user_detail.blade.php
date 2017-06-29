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
                                        ['name' => 'Thông tin cá nhân', 'link' => null],
                                    ]
                                ]
                            )

                <div class="card-body">

                    <div role="tabpanel">
                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs" role="tablist">
                            <li role="presentation" class="active">
                                <a href="#userInfo" aria-controls="home" role="tab" data-toggle="tab"><h4>Thông tin cá nhân</h4></a>
                            </li>

                            <li role="presentation">
                                <a href="#userIntroduce" aria-controls="home" role="tab" data-toggle="tab"><h4>Giới thiệu khách hàng</h4></a>
                            </li>
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane" id="userIntroduce">
                                <div class="row">
                                    <div class="col-sm-4 col-xs-12">

                                        <h4>Link giới thiệu KH</h4>
                                        <!-- 1. Define some markup -->
                                        <span id="_link-user-register">{{ $user_refer['link']  }}</span>
                                        <br>

                                        <button class="btn btn-danger" data-clipboard-action="copy">Copy link</button>

                                    </div>
                                    <div class="col-sm-8 col-xs-12">
                                        <h4>Khách hàng đã giới thiệu</h4>

                                        @if($user_refer['total'] > 0)
                                            <p>Có {{$user_refer['total']}} khách đã được bạn giới thiệu</p>

                                            <table class="table">
                                                <thead>
                                                <tr>
                                                    <th>TT</th>
                                                    <th>Khách hàng</th>
                                                    <th>Số đơn đạt yêu cầu</th>
                                                    <th>Hoa hồng tháng {{ date('m/Y')  }}</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($user_refer['data'] as $key => $val)
                                                <tr>
                                                    <td>{{$key+1}}</td>
                                                    <td>

                                                    </td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        @else

                                            <p>Hiện ban chưa giới thiệu khách nào!</p>
                                        @endif


                                    </div>
                                </div>
                            </div>
                            <div role="tabpanel" class="tab-pane active" id="userInfo">
                                <div class="row">
                                    <div class="col-sm-4 col-xs-12">
                                        <h4>Thông tin chung &nbsp;&nbsp;&nbsp;<small><a href="{{ url('nhan-vien/sua', $user_id)  }}">Sửa</a></small></h4>

                                        <table class="table">

                                            <tbody>
                                            <tr>
                                                <td class="no-padding-leftright"><strong>Họ & tên</strong>: {{$user->name}}</td>
                                            </tr>
                                            <tr>
                                                <td class="no-padding-leftright"><strong>Mã</strong>: {{$user->code}}</td>
                                            </tr>
                                            <tr>
                                                <td class="no-padding-leftright"><strong>Số dư</strong>: {{ App\Util::formatNumber($user->account_balance)  }} <sup>đ</sup></td>
                                            </tr>
                                            <tr>
                                                <td class="no-padding-leftright"><strong>Email</strong>: {{$user->email}}</td>
                                            </tr>

                                            <tr>
                                                <td class="no-padding-leftright"><strong>Trạng thái</strong>: {{ App\User::getStatusName($user->status) }}</td>
                                            </tr>
                                            <tr>
                                                <td class="no-padding-leftright"><strong>Gia nhập</strong>: {{ App\Util::formatDate($user->created_at)  }}</td>
                                            </tr>



                                            </tbody>
                                        </table>


                                    </div>

                                    <div class="col-sm-4 col-xs-12">
                                        <h4>Điện thoại</h4>

                                        @if(!empty($user_mobiles))
                                            <ul id="_list-user-phone" style="list-style: none;margin: 0 0 15px 0;padding: 0;">
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

                        </div>
                    </div>


                </div>
            </div>
        </div>


    </div>
@endsection

@section('js_bottom')
    @parent

    <!-- 2. Include library -->
    <script src="{{ asset('js/clipboard.min.js')  }}"></script>

    <!-- 3. Instantiate clipboard -->
    <script>
        var clipboard = new Clipboard('.btn', {
            target: function() {
                return document.getElementById('_link-user-register');
            }
        });

        clipboard.on('success', function(e) {
            console.log(e);
        });

        clipboard.on('error', function(e) {
            console.log(e);
        });
    </script>

    <script>
        $(document).ready(function(){

            $(document).on('click', '._remove-user-phone', function(){
                var user_phone = $(this).data('phone');
                var user_phone_id = $(this).data('id');

                var $that = $(this);

                $.ajax({
                    url: "{{ url('nhan-vien/dien-thoai') }}",
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
                    url: "{{ url('nhan-vien/dien-thoai')  }}",
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