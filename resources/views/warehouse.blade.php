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
                                        ['name' => 'Quản lý kho hàng', 'link' => null],
                                    ]
                                ]
                            )

                <div class="card-body">

                    <p>
                        Hiện có <strong>5</strong> kho trên hệ thống
                    </p>

                    <a class="btn btn-danger text-uppercase pull-right" data-toggle="modal" href='#modal-id'>Tạo kho</a>

                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th width="30%">Kho</th>
                                <th>Mã</th>
                                <th>Alias</th>
                                <th>Loại</th>
                                <th>Thời gian tạo</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>

                        <tbody>

                        @foreach($data as $idx => $data_item)
                        <tr>
                            <td>
                                {{ $idx+1  }}
                            </td>
                            <td>
                                <strong>{{$data_item->name}}</strong>
                                <p>
                                    <small>
                                        {{$data_item->description}}
                                    </small>
                                </p>
                            </td>
                            <td>
                                <code>{{$data_item->code}}</code>
                            </td>
                            <td>{{$data_item->alias}}</td>
                            <td>{{ App\WareHouse::getTypeNameWarehouse($data_item->type) }}</td>
                            <td>{{ App\Util::formatDate($data_item->created_at)  }}</td>
                            <td>

                                <a href="javascript:void(0)" class="_remove" data-id="{{$data_item->id}}">

                                    <i class="fa fa-times"></i>
                                </a>

                            </td>

                        </tr>
                        @endforeach
                        </tbody>
                    </table>




                    <div class="modal fade" id="modal-id">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                    <h4 class="modal-title">Thông tin kho</h4>
                                </div>
                                <div class="modal-body">


                                    <form id="_frm-warehouse" action="" method="POST" role="form">

                                        <div class="form-group">
                                            <input autofocus type="text" name="name" class="form-control _autofocus" id="" placeholder="Tên kho">
                                        </div>

                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-sm-12 col-xs-12">
                                                    <input type="text" name="code" class="form-control" id="" placeholder="Mã kho">
                                                </div>
                                            </div>


                                        </div>

                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-sm-12 col-xs-12">
                                                    <input type="text" name="alias" class="form-control" id="" placeholder="Alias">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-sm-12 col-xs-12">
                                                    <select name="type" class="form-control" id="">
                                                        <option value="">Loại kho</option>
                                                        @foreach(App\WareHouse::$type_warehouse as $key => $type_warehouse_item)
                                                            <option value="{{$key}}">{{$type_warehouse_item}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <br>

                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-sm-12 col-xs-12">
                                                    <input type="number" name="ordering" class="form-control" id="" placeholder="Thứ tự hiển thị">
                                                </div>
                                            </div>
                                        </div>


                                        <div class="form-group">
                                            <textarea placeholder="Mô tả kho" name="description" class="form-control" id="" cols="30" rows="5"></textarea>
                                        </div>

                                        <input type="hidden" name="_token" value="{{ csrf_token()  }}">
                                    </form>

                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
                                    <button type="button" class="btn btn-danger text-uppercase" id="_save">Lưu</button>
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
    <script>
        $(document).ready(function(){


            $(document).on('click', '._remove', function(){

                var $that = $(this);

                bootbox.confirm('HÀNH ĐỘNG NÀY CỰC KỲ NGUY HIỂM, HÃY CÂN NHẮC TRƯỚC KHI XÓA?', function(result){
                    if(result){
                        var id = $that.data('id');

                        $.ajax({
                            url: "{{ url('warehouse/delete')  }}",
                            method: 'put',
                            data: {
                                id: id,
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
                var $that = $(this);

                console.log('click save');
                $(this).prop('disabled', true);

                var data = $('#_frm-warehouse').serializeObject();

                $.ajax({
                    url: "{{ url('warehouse')  }}",
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

