@extends($layout)

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
                            ['name' => 'Quét mã vạch', 'link' => null],
                        ]
                    ]
                )
                <div class="card-body">

                    <div class="row">
                        <div class="col-sm-4 col-xs-12">
                            <h3>Quét mã vạch</h3>


                            <form onsubmit="return false;" class="___form" id="_from-scan-barcode">

                                <select name="action" id="" class="form-control">
                                    @if(!empty($action_list))
                                        @foreach($action_list as $key => $val)
                                            <option value="{{$key}}">
                                                {{$val}}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                <br>

                                <select name="warehouse" id="" class="form-control">
                                    @if(!empty($warehouse_list))
                                        @foreach($warehouse_list as $key => $val)
                                            <option
                                                    data-warehouse-type="{{$val['type']}}"
                                                    value="{{$val['code']}}">{{$val['name']}} - {{$val['description']}}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <br>

                                <input type="hidden" name="method" value="post">
                                <input type="hidden" name="url" value="{{ url('scan/action') }}">
                                <input type="hidden" name="_token" value="{{ csrf_token()  }}">

                                <input
                                        id="_scan-logistic-package-barcode"
                                        autofocus
                                        type="text"
                                        name="barcode"
                                        class="form-control _scan-barcode"
                                        data-key-global="barcode-scan-input"
                                        placeholder="Quét mã kiện">

                            </form>


                        </div>
                        <div class="col-sm-8 col-xs-12">
                            {{--<h3>Kết quả quét</h3>--}}


                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

@endsection

@section('js_bottom')
    @parent
    <script src="{{asset('js/notify.min.js')}}"></script>
    <script src="{{asset('js/ion.sound.min.js')}}"></script>

    <script>
        $(document).ready(function(){

            // init bunch of sounds
            ion.sound({
                sounds: [
                    {name: "success"},
                    {name: "error"}
                ],

                // main config
                path: "{{ asset('sounds')  }}/",
                preload: true,
                multiplay: true,
                volume: 0.9
            });

            $(document).on('keypress', '._scan-barcode', function(e){
                var that = this;
                if(e.keyCode == 13){
                    var barcode = $(this).val();
                    if(!barcode) return false;

                    request("{{ url('scan/action') }}", "post", $('#_from-scan-barcode').serializeObject()).done(function(response){
                        var msg_type = 'success';
                        if(response.success){
                            ion.sound.play("success");
                        }else{
                            msg_type = 'error';
                            ion.sound.play("error");
                        }

                        if(response.message){
                            $.notify(response.message, msg_type);
                        }
                        $(that).val('').focus();
                    });
                }
            });

//            $(document).on('change', 'select[name="warehouse"]', function(){
//                var warehouse_type = $(this).data('warehouse-type');
//                Action.chooseWarehouse(warehouse_type);
//            });

        });

        {{--var Action = {--}}
            {{--chooseWarehouse: function (warehouse_type) {--}}
                {{--var $dom = $('select[name="action"]');--}}
                {{--$dom.find('option').remove();--}}
                {{--if(warehouse_type == "{{ App\WareHouse::TYPE_DISTRIBUTION  }}"){--}}
                    {{--$dom.append('<option value="IN">Nhập</option>');--}}
                    {{--$dom.append('<option value="OUT">Xuất</option>');--}}
                {{--}else if(warehouse_type == "{{ App\WareHouse::TYPE_RECEIVE  }}"){--}}
                    {{--$dom.append('<option value="OUT">Xuất</option>');--}}
                {{--}--}}
            {{--}--}}
        {{--};--}}

        {{--Action.chooseWarehouse("{{ App\WareHouse::TYPE_RECEIVE  }}");--}}

    </script>
@endsection

