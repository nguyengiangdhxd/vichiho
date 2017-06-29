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
                            ['name' => 'Quản lý link đặt hàng báo lỗi', 'link' => null],
                        ]
                    ]
                )
                <div class="card-body">

                    <h3>
                        Quản lý link đặt hàng báo lỗi
                    </h3>

                    <form onchange="this.submit();" action="{{ url('manager_addon_link_error')  }}" method="get">
                        <select name="is_done" id="">
                            @foreach($is_done_list as $k => $v)
                                <option @if(isset($condition['is_done'])
                                && $condition['is_done'] == $k) selected @endif
                                        value="{{$k}}">{{$v}}</option>
                            @endforeach

                        </select>
                        <input type="submit" value="Tìm kiếm">
                    </form>

                    <p>
                        Tìm thấy {{$total_records}} link báo lỗi
                    </p>

                    @if($total_records)
                        <table class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>TT</th>
                                    <th>Site</th>
                                    <th>Link</th>
                                    <th>Người báo</th>
                                    <th>Thời gian</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach($data as $key => $item)
                                    <tr>
                                        <td>{{$key+1}}</td>
                                        <td>
                                            {{$item->site}}
                                        </td>
                                        <td>
                                            <a href="{{$item->link}}" target="_blank">
                                                {{$item->link}}
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{{ url('user/detail', $item->create_user->id)  }}">{{$item->create_user->email}}</a> ({{$item->create_user->code}})
                                        </td>
                                        <td>
                                            {{App\Util::formatDate($item->created_at)}}
                                        </td>
                                        <td>
                                            @if($item->is_done)
                                                Đã xử lý xong

                                            @else
                                                <a href="javascript:void(0)"
                                                   class="_click-done-item"
                                                   data-id="{{$item->id}}"><i class="fa fa-check"></i></a>
                                            @endif

                                        </td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                    @else

                        <p>Hiện chưa có link báo lỗi nào</p>

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

            $(document).on('click', '._click-done-item', function(){
                var that = this;
                var id = $(this).data('id');
                if(!id){
                    return false;
                }

                $.ajax({
                  url: "{{ url('set_done_link_error')  }}",
                  method: "post",
                  data: {
                      _token: "{{csrf_token()}}",
                      id:id
                  },
                  success:function(response) {
                      if(response.success){
                          $(that).parent().text('Đã xử lý xong');
                      }
                  },
                  error: function(){
                    
                  }
                });
            });
        });

    </script>
@endsection

