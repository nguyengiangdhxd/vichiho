@extends($layout)

@section('page_title')
    {{@$page_title}}
@endsection

@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                @include('partials/__breadcrumb',
                    [
                        'urls' => [
                            ['name' => 'Trang chủ', 'link' => url('home')],
                            ['name' => 'Kiện hàng', 'link' => url('packages')],
                            ['name' => 'Tạo kiện', 'link' => null],
                        ]
                    ]
                )
                <div class="card-body">


                    <div class="row">
                        <div class="col-xs-12">
                            <h3>Tạo kiện</h3>

                            <form class="___form" onsubmit="return false;">

                                <input type="hidden" name="method" value="post">
                                <input type="hidden" name="url" value="{{ url('package/action') }}">
                                <input type="hidden" name="_token" value="{{ csrf_token()  }}">
                                <input type="hidden" name="response" value="package_add">
                                <input type="hidden" name="action" value="create_package">

                                <input
                                        autofocus
                                        type="text"
                                        name="barcode"
                                        id="_barcode"
                                        class="form-control _______input-action"
                                        data-key-global="barcode-scan-input-create-package"
                                        placeholder="Quét mã vận đơn...">

                            </form>

                            @if($barcode)
                                <h5>Mã quét: {{$barcode}}</h5>
                            @endif
                        </div>

                    </div>


                </div>
            </div>
        </div>
        <div class="col-md-8 list-packages-view">
            @if(!empty($barcode))

                @if(count($packages))
                    @foreach($packages as $package)
                        <div class="card _package" data-package-id="{{ $package->id  }}">
                            <div class="card-header">
                                <h3 style="margin: 0">
                                    Kiện hàng #<a href="{{ url('package', $package->logistic_package_barcode)  }}">{{$package->logistic_package_barcode}}</a>
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-sm-12 col-xs-12">

                                        <form class="_package-item-form" action="" method="post" onsubmit="return false;">
                                            {{csrf_field()}}

                                            <input type="hidden" name="action" value="update_package">
                                            <input type="hidden" name="package_id" value="{{ $package->id  }}">

                                            <ul class="form-list-item">
                                                @if($package->order)
                                                    <li>
                                                        <strong>Đơn hàng</strong>: <a href="{{ url('order', $package->order->id)  }}" target="_blank">{{$package->order->code}}</a>, kho đích {{$package->order->destination_warehouse}}
                                                    </li>
                                                    <li>
                                                        <strong>Thông tin nhận hàng</strong>:
                                                        <i class="fa fa-user"></i> {{$package->customer_address->reciver_name}} - <i class="fa fa-phone"></i> {{$package->customer_address->reciver_phone}}
                                                        <i class="fa fa-map-marker"></i> {{$package->customer_address->detail}}, {{$package->customer_address->district_label}}, {{$package->customer_address->province_label}}
                                                    </li>
                                                @else
                                                    <li>
                                                        <strong>Đơn hàng</strong>: --
                                                    </li>
                                                    <li>
                                                        <strong>Thông tin nhận hàng</strong>: ---
                                                    </li>
                                                @endif

                                                <li>
                                                    <strong>Cân nặng (kg):</strong>

                                                    Tịnh
                                                    @if(!$package->weight_type || $package->weight_type == 1)
                                                        <input type="radio" checked="checked" name="weight_type_{{$package->id}}" value="1">
                                                    @else
                                                        <input type="radio" name="weight_type_{{$package->id}}" value="1">
                                                    @endif

                                                    <input value="{{ $package->weight  }}" name="weight" type="text" style="width: 15%;" class="!form-control">

                                                    Quy đổi
                                                    @if($package->weight_type == 2)
                                                        <input type="radio" checked="checked" name="weight_type_{{$package->id}}" value="2">
                                                    @else
                                                        <input type="radio" name="weight_type_{{$package->id}}" value="2">
                                                    @endif

                                                    <input value="{{ $package->converted_weight  }}" name="converted_weight" disabled type="text" style="width: 15%;" class="!form-control">
                                                </li>

                                                <li>
                                                    <strong>Thể tích (cm):</strong>
                                                    <input value="{{$package->length_package}}" name="length_package" type="text" style="width: 10%;" class="!form-control" placeholder="Dài">
                                                    x<input value="{{$package->width_package}}" name="width_package" type="text" style="width: 10%;" class="!form-control" placeholder="Rộng">
                                                    x<input value="{{$package->height_package}}" name="height_package" type="text" style="width: 10%;" class="!form-control" placeholder="Cao">

                                                </li>
                                                <li>
                                                    <strong>Ghi chú:</strong>
                                                    <textarea name="note" id="" cols="30" rows="3" class="form-control">{{ $package->note  }}</textarea>
                                                </li>
                                                <li>
                                                    <iframe
                                                            style="display: none!important;" src="" frameborder="0"></iframe>

                                                    <a style="margin-right: 10px;"
                                                       class="btn-link _print"
                                                       href="javascript:void(0)"><i class="fa fa-print"></i> In tem</a>

                                                    <a style="margin-right: 10px;"
                                                       class="btn-link _link-in"
                                                       target="_blank"
                                                       href="{{ url('package?action=print&logistic_package_barcode=' . $package->logistic_package_barcode)  }}">Link In</a>
                                                    &nbsp;&nbsp;&nbsp;
                                                    @if(!$package->order)
                                                    <a style="margin-right: 10px;" class="btn-link _delete-package"
                                                       data-package-id="{{$package->id}}"
                                                       href="javascript:void(0)">Xóa</a>
                                                    @endif
                                                    &nbsp;&nbsp;&nbsp;
                                                </li>
                                            </ul>
                                        </form>

                                    </div>
                                </div>


                            </div>

                        </div>
                    @endforeach
                @endif
            @endif
        </div>
    </div>

@endsection

@section('js_bottom')
    @parent
    <script>
        $(document).ready(function(){

            $(document).on('click', '._print', function(e){
                var parent = $(this).parents('._package');
                var href = parent.find('._link-in').attr('href');
                if(href){
                    parent.find('iframe').attr('src', href);
                }
            });

            $(document).on('keypress', '#_barcode', function(e){
               if(e.keyCode == 13){
                   var barcode = $(this).val();
                   if(!barcode) return false;

                   $.ajax({
                     url: "{{ url('package/action') }}",
                     method: 'post',
                     data: {
                         barcode:barcode,
                         _token: "{{csrf_token()}}",
                         action: 'create_package',
                     },
                     success:function(response) {

                         if(response.success){
                             window.location.href = "{{ url('package?barcode=')  }}" + barcode;
                         }else{
                             if(response.message){
                                 bootbox.alert(response.message);
                             }
                         }
                     },
                     error: function(){

                     }
                   });
               }
            });

            $(document).on('change', '._package-item-form', function(){
                var data_send = $(this).serializeObject();
                $.ajax({
                  url: "{{ url('package/action')  }}",
                  method: 'post',
                  data: data_send,
                  success:function(response) {
                      if(response.success){

                          $('._package[data-package-id="' + response.result.package.id + '"]').find('input[name="converted_weight"]').val(response.result.package.converted_weight);
                      }else{
                          if(response.message){
                              bootbox.alert(response.message);
                          }
                      }
                  },
                  error: function(){

                  }
                });
            });

            $(document).on('click', '._delete-package', function(){
                var package_id = $(this).data('package-id');
                $.ajax({
                    url: "{{ url('package/action')  }}",
                    method: 'post',
                    data: {
                        _token: "{{csrf_token()}}",
                        action: 'delete_package',
                        package_id:package_id,
                    },
                    success:function(response) {
                        if(response.success){
                            $('._package[data-package-id="' + package_id + '"]').remove();
                        }else{
                            if(response.message){
                                bootbox.alert(response.message);
                            }
                        }
                    },
                    error: function(){

                    }
                });
            });
        });

    </script>
@endsection

@section('css_bottom')
    @parent
    <style>
        .list-packages-view .card{
            margin-bottom: 15px;
        }

        .list-packages-view .card:nth-child(2n) .card-header{
            /*background: #29c75f!important;*/
            /*color: #fff!important;*/
        }
    </style>
@endsection

