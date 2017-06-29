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
                                        ['name' => 'Cấu hình kho', 'link' => null],
                                    ]
                                ]
                            )

                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-3 col-xs-12">

                            @if($can_add_new)

                            <h4>Thêm cấu hình kho bằng tay</h4>
                            <br>
                            <form id="_user-warehouse-frm" action="" method="POST" role="form">

                                <div class="form-group">
                                    <select name="user_id" class="form-control" id="">
                                        <option value="">Khách hàng</option>
                                        @if($users)
                                            @foreach($users as $user)
                                                <option value="{{$user->id}}">{{$user->email}} - {{$user->name}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <br>
                                <div class="form-group">
                                    <select name="warehouse_code" class="form-control" id="">
                                        <option value="">Kho đích</option>
                                        @if($warehouses)
                                            @foreach($warehouses as $warehouse)
                                                <option data-alias="{{$warehouse->alias}}" value="{{$warehouse->code}}">{{$warehouse->code}} - {{$warehouse->name}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <br>

                                <input type="hidden" name="_token" value="{{csrf_token()}}">

                                <button type="button" class="btn btn-danger text-uppercase" id="_save">Lưu</button>
                            </form>

                            @endif

                        </div>
                        <div class="col-sm-9 col-xs-12">

                            <h4>Danh sách cấu hình kho</h4>
                            <br>

                            <p>Tìm thấy <strong>{{ count($user_warehouse)  }}</strong> cấu hình</p>

                            <table class="table">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Khách hàng</th>
                                    <th>Kho đích</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>

                                @if(count($user_warehouse))
                                    @foreach($user_warehouse as $idx => $user_warehous)

                                        <tr>
                                            <td>
                                                {{$idx + 1}}
                                            </td>
                                            <td>
                                                <a href="{{ url('user/detail', $user_warehous->user_id)  }}">
                                                    {{$user_warehous->email}} ({{$user_warehous->user_name}})
                                                </a>
                                            </td>
                                            <td>
                                                <code>{{$user_warehous->warehouse_code}}</code> - {{$user_warehous->name}}
                                            </td>
                                            <td>

                                                @if($can_remove)
                                                    <a

                                                            data-user-id="{{$user_warehous->user_id}}"
                                                            data-warehouse-code="{{$user_warehous->warehouse_code}}"
                                                            href="javascript:void(0)" class="_remove">
                                                        <i class="fa fa-times"></i>
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif

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

            $(document).on('click', '._remove', function(){

                var $that = $(this);

                bootbox.confirm('Bạn có chắc muốn xóa?', function(result){
                    if(result){
                        var user_id = $that.data('user-id');
                        var warehouse_code = $that.data('warehouse-code');


                        $.ajax({
                            url: "{{ url('warehouses_manually/delete')  }}",
                            method: 'put',
                            data: {
                                user_id: user_id,
                                warehouse_code: warehouse_code,
                                _token: "{{ csrf_token() }}"
                            },
                            success:function(response) {
                                if(response.success){
                                    window.location.reload();
                                }else{
                                    bootbox.alert(response.message);
                                }
                            },
                            error: function(){

                            }
                        });
                    }
                });



            });

            $(document).on('click', '#_save', function(){
//                console.log('click save');
                var $that = $(this);

                $(this).prop('disabled', true);

                var data = $('#_user-warehouse-frm').serializeObject();

                $.ajax({
                    url: "{{ url('warehouses_manually')  }}",
                    method: 'post',
                    data: data,
                    success:function(response) {
                        if(response.success){
                            window.location.reload();
                        }else{
                            bootbox.alert(response.message);
                            $that.prop('disabled', false);
                        }
                    },
                    error: function(){
                        $that.prop('disabled', false);
                    }
                });
            });

        });

    </script>
@endsection

