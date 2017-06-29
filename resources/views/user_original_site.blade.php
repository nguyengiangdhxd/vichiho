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

                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-5 col-xs-12">
                            <h4>
                                Tìm thấy <strong>{{count($data)}}</strong> kết quả
                            </h4>

                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Site</th>
                                    <th>Username</th>
                                    <th>Thời gian tạo</th>
                                    <th></th>
                                </tr>
                                </thead>

                                <tbody>
                                @foreach($data as $date_item)
                                    <tr>
                                        <td>{{ $date_item->site  }}</td>
                                        <td>{{ $date_item->username  }}</td>
                                        <td>{{ App\Util::formatDate($date_item->created_at)  }}</td>
                                        <td>
                                            <a href="javascript:void(0)" class="_remove"
                                            data-site="{{ $date_item->site  }}"
                                            data-username="{{ $date_item->username  }}"
                                            >
                                                <i class="fa fa-times"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="col-sm-5 col-xs-12">
                            <h4>Thêm user mới</h4>

                            <input required name="username" id="_username" autofocus type="text">

                            <select required name="site" id="_site">
                                <option value="">Chọn site</option>
                                @foreach(App\User::$site_list as $key => $site_item)
                                    <option value="{{$key}}">{{$site_item}}</option>
                                @endforeach
                            </select>

                            <a href="javascript:void(0);" id="_save">Lưu</a>

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
                        var username = $that.data('username');
                        var site = $that.data('site');

                        $.ajax({
                            url: "{{ url('user/original_site/delete')  }}",
                            method: 'put',
                            data: {
                                username: username,
                                site: site,
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
                console.log('click save');
                var $that = $(this);

                if($(this).hasClass('disabled')) return false;

                $(this).addClass('disabled');

                $.ajax({
                  url: "{{ url('user/original_site')  }}",
                  method: 'post',
                  data: {
                      username: $('#_username').val(),
                      site: $('#_site').val(),
                      _token: "{{ csrf_token() }}"
                  },
                  success:function(response) {
                        if(response.success){
                            window.location.reload();
                        }else{
                            bootbox.alert(response.message);
                            $that.removeClass('disabled');
                        }
                  },
                  error: function(){
                      $that.removeClass('disabled');
                  }
                });
            });
        });
    </script>
@endsection

