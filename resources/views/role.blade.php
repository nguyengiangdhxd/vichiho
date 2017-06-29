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
                                        ['name' => $page_title, 'link' => null],
                                    ]
                                ]
                            )

                <div class="card-body"><div class="row">



                        <div class="col-md-8">


                            <a class="btn btn-danger text-uppercase pull-right" data-toggle="modal" href='#modal-id'>Tạo nhóm </a>
                            <div class="modal fade" id="modal-id">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                            <h4 class="modal-title">Tạo nhóm </h4>
                                        </div>
                                        <div class="modal-body">

                                            <form action="" method="post" id="_from-role">
                                                <input type="text" class="form-control _autofocus" name="label" placeholder="Tên nhóm ">
                                                <textarea name="description" rows="3" class="form-control" placeholder="Mô tả nhóm "></textarea>
                                                <select name="state" class="form-control" id="">
                                                    @foreach(App\Role::$stateList as $key => $value)
                                                        <option value="{{$key}}">{{$value}}</option>
                                                    @endforeach
                                                </select>


                                            </form>

                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Huỷ </button>
                                            <button type="button" class="btn btn-primary" id="_save-role">Lưu </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <table class="table">
                                <thead>
                                <tr>
                                    <th width="5%">ID</th>
                                    <th width="50%">Nhóm </th>
                                    <th width="25%">Trạng thái </th>
                                    <th width="20%" class="text-center">Thao tác </th>
                                </tr>
                                </thead>
                                <tbody>
                                @if(!empty($roles))

                                    @foreach($roles as $role)
                                        <tr>
                                            <td>{{$role['id']}}</td>
                                            <td>
                                                <a href="{{ url('setting/role', $role['id'])  }}">{{$role['label']}}</a>
                                                <p>
                                                    <small>
                                                        {{$role['description']}}
                                                    </small>

                                                </p>
                                            </td>
                                            <td>{{App\Role::getStateName($role['state'])}}</td>
                                            <td class="text-center">
                                                <a href="javascript:void(0)" class="_delete-role" data-id="{{$role['id']}}">
                                                    <i class="fa fa-times"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach

                                @endif


                                </tbody>
                            </table>


                        </div>
                        <div class="col-md-4">

                            @include('partials/__permissions', ['can_edit' => 0])

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

            $(document).on('click', '#_save-role', function(e){
                var $that = $(this);

                $(this).prop('disabled', true);

                var data_send = $('#_from-role').serializeObject();
                    data_send._token = "{{ csrf_token() }}";

                $.ajax({
                    url: "{{ url('setting/role') }}",
                    method: 'post',
                    data: data_send,
                    success: function(response){


                        if(response.success){
                            window.location.reload();
                        }else{
                            bootbox.alert({
                                message: response.message,
                                size: 'small'
                            });

                            $that.prop('disabled', false);
                        }
                    },
                    error: function () {
                        $that.prop('disabled', false);
                    }
                })
            });

            $(document).on('click', '._delete-role', function(e){
                var self = this;
                bootbox.confirm("Are you sure?", function(result){
                    if(result){
                        var id = $(self).data('id');
                        $.ajax({
                            url: "{{ url('setting/role/delete') }}",
                            method: 'put',
                            data: {
                                "_token": "{{ csrf_token() }}",
                                id:id
                            },
                            success: function(response){


                                if(response.success){
                                    window.location.reload();
                                }else{
                                    bootbox.alert({
                                        message: response.message,
                                        size: 'small'
                                    });
                                }


                            },
                            error: function () {

                            }
                        })
                    }
                });
            });

        });

    </script>
@endsection

