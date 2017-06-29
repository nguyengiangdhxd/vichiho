@extends('layouts.app')

@section('page_title')
    {{$page_title}}
@endsection

@section('content')




    <div class="row">


        <div class="col-sm-6">



            <div class="card">
                @include('partials/__breadcrumb',
                                                [
                                                    'urls' => [
                                                        ['name' => 'Trang chủ', 'link' => url('home')],
                                                        ['name' => 'Nhóm & phân quyền', 'link' => url('setting/roles')],
                                                        ['name' => 'Chi tiết nhóm' . $page_title, 'link' => null],
                                                    ]
                                                ]
                                            )
                <div class="card-body">

                    @include('partials/__permissions', ['can_edit' => 1])
                </div>
            </div>

        </div>

        <div class="col-sm-6">
            <div class="panel panel-danger panel-nhatminh">
                <div class="panel-heading">{{$page_title}}</div>
                <div class="panel-body">


                    <form action="{{ url('setting/role/update', $role_id)  }}" method="post">
                        <input value="{{ $role->label  }}" type="text" class="form-control" autofocus name="label" placeholder="Tên nhóm">
                        <textarea name="description" rows="3" class="form-control" placeholder="Mô tả nhóm">{{$role->description}}</textarea>
                        <select style="margin-bottom: 15px;" name="state" class="form-control" id="">
                            @foreach(App\Role::$stateList as $key => $value)
                                <option @if($key == $role->state) selected @endif value="{{$key}}">{{$value}}</option>
                            @endforeach
                        </select>

                        <input type="hidden" name="role_id" value="{{$role_id}}">
                        {{ csrf_field() }}

                        <button type="submit" class="btn btn-danger" id="_save-role">Cập nhật</button>
                    </form>
                </div>
            </div>


            <div class="panel panel-danger panel-nhatminh">
                <div class="panel-heading">Thành viên thuộc nhóm ({{ count($users_in_role)  }})</div>
                <div class="panel-body">

                    @if(!empty($users_in_role))
                        <ul style="list-style: none">
                            @foreach($users_in_role as $kk => $vv)
                                <li>

                                    <a data-toggle="tooltip" title="Xoá thành viên ra khỏi nhóm" data-role-id="{{$role_id}}" data-user-id="{{$vv['id']}}" href="javascript:void(0)" data-action="remove" class="_change-user-role"><i class="fa fa-minus"></i></a>
                                    &nbsp;&nbsp;&nbsp;
                                    <strong>{{ $vv['email']  }}</strong> ({{ $vv['name']  }})


                                </li>
                            @endforeach
                        </ul>
                    @endif

                </div>
            </div>

            <div class="panel panel-danger panel-nhatminh" style="">
                <div class="panel-heading" style="">Thành viên không thuộc nhóm ({{ count($users_not_in_role)  }})</div>
                <div class="panel-body">

                    @if(!empty($users_not_in_role))
                        <ul style="list-style: none">
                            @foreach($users_not_in_role as $kkk => $vvv)
                                <li>
                                    <a data-toggle="tooltip" title="Thêm thành viên vào nhóm" data-role-id="{{$role_id}}" data-user-id="{{$vvv['id']}}" href="javascript:void(0)" data-action="add" class="_change-user-role"><i class="fa fa-plus"></i></a>
                                    &nbsp;&nbsp;&nbsp;
                                    <strong>{{ $vvv['email']  }}</strong> ({{ $vvv['name']  }})

                                </li>
                            @endforeach
                        </ul>
                    @endif

                </div>
            </div>
        </div>


    </div>
@endsection

@section('js_bottom')
    @parent
    <script>
        $(document).ready(function(){

            $(document).on('click', '._change-user-role', function(e){

                $.ajax({
                    url: "{{ url('setting/role/user') }}",
                    method: 'post',
                    data: {
                        role_id: "{{$role_id}}",
                        action: $(this).data('action'),
                        user_id: $(this).data('user-id'),
                        _token:"{{ csrf_token() }}"
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
            });

            $(document).on('click', '#_save-permission', function(e){
                var $that = $(this);
                $(this).prop('disabled', true);

                var permission = [];
                $('._set-permission:checked').each(function(i){
                    permission.push($(this).val());
                });

                $.ajax({
                    url: "{{ url('setting/role/permission') }}",
                    method: 'post',
                    data: {
                        role_id: "{{$role_id}}",
                        permission:permission,
                        _token:"{{ csrf_token() }}"
                    },
                    success: function(response){

//                        bootbox.alert({
//                            message: response.message,
//                            size: 'small'
//                        });

                        $that.prop('disabled', false);
                    },
                    error: function () {
                        $that.prop('disabled', false);
                    }
                })
            });

        });

    </script>
@endsection

