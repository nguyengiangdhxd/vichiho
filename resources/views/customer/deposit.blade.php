@extends('layouts.app')

@section('page_title')
    {{$page_title}}
@endsection

@section('content')
    <div class="row">
        <div class="col-xs-12">
            @include('partials/__cart_step', ['active' => 2])
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">

                            <h3>Địa chỉ nhận hàng</h3>
                            <br>

                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>TT</th>
                                        <th>Thông tin địa chỉ</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>


                                @foreach($user_address as $idx => $user_address_item)

                                    <?php

                                    $province = App\Location::find($user_address_item->province_id)->label;
                                    $district = App\Location::find($user_address_item->district_id)->label;

                                    ?>

                                    <tr class="_user-address"
                                         data-is-default="{{ $user_address_item->is_default }}"
                                         data-id="{{$user_address_item->id}}">
                                        <th width="5%">{{ $idx + 1 }}</th>
                                        <td width="60%">
                                            <p>
                                                <strong>
                                                    {{$user_address_item->reciver_name}} / {{$user_address_item->reciver_phone}}
                                                </strong>

                                                @if($user_address_item->is_default)
                                                    <span class="label label-danger text-uppercase">Mặc định</span>
                                                @endif
                                            </p>

                                            <p>{{$user_address_item->detail}}, {{$district}}, {{$province}}</p>
                                        </td>
                                        <td>
                                            <a data-toggle="tooltip" title="Sửa địa chỉ" href="javascript:void(0)" data-id="{{$user_address_item->id}}" data-json="{{ json_encode($user_address_item)  }}" class="_btn-action-edit">
                                                <i class="fa fa-pencil"></i>
                                            </a>
                                            &nbsp;&nbsp;
                                            <a data-toggle="tooltip" title="Xóa địa chỉ" href="javascript:void(0)" data-id="{{$user_address_item->id}}" class="_btn-action-delete">
                                                <i class="fa fa-times"></i>
                                            </a>
                                            &nbsp;&nbsp;
                                            @if(!$user_address_item->is_default)
                                            <a data-toggle="tooltip" title="Đặt mặc định" href="javascript:void(0)" data-id="{{$user_address_item->id}}" class="_btn-action-set-default">
                                                <i class="fa fa-check"></i>
                                            </a>
                                            @endif
                                        </td>


                                    </tr>

                                @endforeach

                                </tbody>
                            </table>


                            @if($max_user_address)
                                <a class="btn btn-primary" id="_add-user-address">Thêm địa chỉ</a>
                            @endif

                            <div class="modal fade" id="modal-id">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                            <h4 class="modal-title">Cập nhật địa chỉ nhận hàng</h4>
                                        </div>
                                        <div class="modal-body">

                                            <form id="_form-update-user-address" action="{{ url('user/address')  }}" method="post">

                                            <div style="margin-bottom: 15px;">
                                                <select required id="province_id" autofocus name="province_id" class="_autofocus form-control" id="">
                                                    <option value="">Tỉnh / thành phố</option>
                                                    @foreach($all_provinces as $province)
                                                        <option value="{{$province->id}}">{{$province->label}}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div style="margin-bottom: 15px;">
                                                <select required id="district_id" name="district_id" class="form-control" id="">
                                                    <option value="">Quận / huyện</option>
                                                    @foreach($all_districts as $district)
                                                        <option class="hidden" data-province-id="{{ $district->parent_id  }}" value="{{$district->id}}">{{$district->label}}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <input required type="text" name="detail" class="form-control" placeholder="Địa chỉ">

                                            <input required type="text" id="reciver_name" name="reciver_name" class="form-control" placeholder="Tên người nhận">

                                            <input required type="text" name="reciver_phone" class="form-control" placeholder="Điện thoại">

                                            <textarea name="note" rows="3" class="form-control" placeholder="Ghi chú"></textarea>

                                            <input type="hidden" name="user_address_id" id="user_address_id" value="0">

                                            </form>

                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
                                            <button type="button" class="btn btn-primary" id="_btn-update-user-address">Cập nhật</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="col-md-6">

                            <h3>Danh sách shop kết đơn ({{count($shops)}})</h3>
                            <br>

                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Shop</th>
                                    <th>SL / Link</th>
                                    <th>Tiền hàng</th>
                                </tr>
                                </thead>
                                <tbody>

                                @if(count($shops))

                                    @foreach($shops as $shop)
                                    <tr class="_shop-item" data-json="{{ json_encode($shop)  }}">
                                        <td>
                                        <?php
                                            $avatar = urldecode($shop->avatar);
                                        ?>
                                            <img style="width: 50px;
    margin-right: 15px; float: left;" src="{{ $avatar }}" alt="">
                                            <div>
                                                <span class="text-uppercase">[{{$shop->site}}]</span> <strong>{{$shop->shop_name}}</strong>
                                            </div>
                                        </td>
                                        <td>{{ $shop->total_quantity  }} / {{ $shop->total_link  }}</td>
                                        <td>{{ App\Util::formatNumber($shop->total_amount)  }} <sup>d</sup></td>
                                    </tr>

                                    @endforeach


                                    <tr>
                                        <td colspan="3">
                                            Tổng tiền hàng: {{ App\Util::formatNumber($total_amount_shop) }} <sup>d</sup>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3">
                                            Đặt cọc ({{$deposit_percent}}%): {{ App\Util::formatNumber($deposit_amount) }} <sup>d</sup>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3">
                                            Số dư hiện tại: {{ App\Util::formatNumber(Auth::user()->account_balance) }} <sup>d</sup>
                                        </td>
                                    </tr>
                                @endif


                                </tbody>
                            </table>


                            @if(Auth::user()->account_balance >= $deposit_amount)

                                <form action="">
                                    <div class="col-sm-4 col-xs-12"><input placeholder="Nhập mật khẩu" type="password" class="form-control _input-password" autofocus></div>
                                    <div class="col-sm-4 col-xs-12">
                                        <input type="button" class="btn btn-danger btn-sm _action-deposit" value="ĐẶT CỌC">
                                    </div>

                                </form>

                            @else

                                <p class="text-danger">
                                    Hiện số tiền trong tài khoản không đủ để đặt cọc. <br>
                                    Hiện bạn còn thiếu {{  App\Util::formatNumber(abs(Auth::user()->account_balance - $deposit_amount)) }} <sup>d</sup> <br>
                                    Vui lòng nạp tiền vào tài khoản để tiến hành đặt cọc đơn hàng. <br>
                                    Liên hệ HOTLINE 04.2262.6699 - 04.2265.6699 để được hỗ trợ!
                                </p>

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


            $(document).on('keypress', '._input-password', function(e){
                 if(e.keyCode == 13){
                     e.preventDefault();
                     $('._action-deposit').click();
                 }
            });

            $(document).on('click', '._action-deposit', function () {
                 var $that = $(this);



                 var password = $('._input-password').val();

                 var address_id = $('._user-address[data-is-default=1]').data('id');

                 if(!$('._user-address').length){
                     bootbox.alert('Hiện chưa có địa chỉ nhận hàng!');
                     return false;
                 }

                 if(!address_id){
                     bootbox.alert('Vui lòng thiết lập 1 địa chỉ nhận hàng làm mặc định!');
                     return false;
                 }

                $that.prop('disabled', true);

                 $.ajax({
                   url: "{{ url('dat-coc')  }}",
                   method: 'post',
                   data: {
                       password: password,
                       shop_id: nhatminh247.shop_id,
                       address_id: address_id,
                       _token: "{{csrf_token()}}"
                   },
                   success:function(response) {
                       if(!response.success){

                           $that.prop('disabled', false);
                           bootbox.alert(response.message);
                           $('._input-password').focus();
                       }else{
                           //bootbox.alert(response.message);
                           window.location.href = response.redirect_url;
                       }

                   },
                   error: function(){
                       $that.prop('disabled', false);
                   }
                 });
            });

            $(document).on("change", "#province_id", function(event){
                var province_id = $(this).val();
                $('#district_id option:first').prop('selected', true);
                $('#district_id option:not(:first)').prop('selected', false);
                showDistrictByProvince(province_id);
            });

            function showDistrictByProvince(province_id){
                $('#district_id option:first').removeClass('hidden');
                $('#district_id option:not(:first)').addClass('hidden');

                if(province_id){
                    $('#district_id option[data-province-id=' + province_id + ']').removeClass('hidden');
                }
                $('#district_id').trigger('change');
            }

            $(document).on('click', '._btn-action-set-default', function () {
                var user_address_id = $(this).data('id');
                $.ajax({
                    url: "{{ url('user/address/default')  }}",
                    method: 'put',
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: user_address_id
                    },
                    success: function(response){
                        if(response.success){
                            window.location.reload();
                        }
                    },
                    error: function () {

                    }
                });
            });

            $(document).on('click', '._btn-action-delete', function () {
                var user_address_id = $(this).data('id');
                bootbox.confirm("Bạn có chắc muốn xóa địa chỉ này?", function(result){
                    if(result){
                        $.ajax({
                            url: "{{ url('user/address/delete')  }}",
                            method: 'put',
                            data: {
                                _token: "{{ csrf_token() }}",
                                action: 'delete',
                                id: user_address_id
                            },
                            success: function(response){
                                if(response.success){
                                    window.location.reload();
                                }
                            },
                            error: function () {

                            }
                        });
                    }
                });
            });

            $(document).on('click', '._btn-action-edit', function(event){
                var id = $(this).data('id');
                var data_json = $(this).data('json');

                $('#_form-update-user-address').setFormData(data_json);

                showDistrictByProvince(data_json.province_id);

                $('#user_address_id').val(id);
                $('#modal-id').modal('show');
            });

            $(document).on('click', '#_add-user-address', function(event){

                $('#_form-update-user-address').setFormData({
                    province_id:'',
                    district_id:'',
                    note:'',
                    reciver_phone:'',
                    reciver_name:'',
                    detail:''
                });

                showDistrictByProvince(0);

                $('#user_address_id').val(0);
                $('#modal-id').modal('show');
            });

            $(document).on('click', '#_btn-update-user-address', function () {

                var data = $('#_form-update-user-address').serializeObject();
                    data._token = "{{ csrf_token() }}";

                $.ajax({
                    url: "{{ url('user/address')  }}",
                    method: 'post',
                    data:data,
                    success: function(response){
                        if(response.success){
                            window.location.reload();
                        }else{
                            bootbox.alert(response.message);
                        }
                    },
                    error: function () {

                    }
                });
            });

        });

    </script>
@endsection

