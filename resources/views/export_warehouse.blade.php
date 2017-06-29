@extends('layouts.app')

@section('page_title')
   {{$page_title}}
@endsection

@section('content')

    <!--html in here-->
    <div class="col-lg-12 margin-top-1" id="_content_warehouse">
        <div class="margin-auto">
            <div class="col-lg-4 sidebar-left">
                <h4 class="text-uppercase font-bold margin-bottom-30">Quét mã vạch</h4>
                    <select class="form-control" id="_action_warehouse" v-model="action_warehouse">
                        <option value="0" selected>Hành Động</option>
                        <option value="IN">Nhập kho</option>
                        <option value="OUT">Xuất kho</option>
                    </select>
                <p></p>

                <select class="form-control" id="_current_warehouse" v-model="current_warehouse">
                    <option value="0" selected>Kho Hàng</option>
                    <option value="CNGZ">CNGZ</option>
                    <option value="VNHN">VNHN</option>
                    <option value="VNSG">VNSG</option>
                </select>


                <input class="margin-top-12 custom-input _input-barcode" name="input" v-model="barcode"
                       v-on="keyup:addLang(lang, $event)"
                       type="text" value="" placeholder="NHẬP MÃ VẠCH" />

                <div class="context-total-barcode"><span class="total-barcode font-bold">34</span> mã đã quét</div>

                <div class="box-sidebar">
                    <h4 class="title-box-sidebar font-bold">MÃ BỘ PHẬN</h4>
                    <ul class="list-box-sidebar">
                        <li><span class="font-bold">CNGZ</span> - <span class="font-color-898989">Kho nhận Quảng Châu</span></li>
                        <li><span class="font-bold">VNHN</span> - <span class="font-color-898989">Kho phân phối Hà Nội</span></li>
                        <li><span class="font-bold">VNSG</span> - <span class="font-color-898989">Kho phân phối Sài Gòn</span></li>
                    </ul>
                </div>

                <div class="box-sidebar">
                    <h4 class="title-box-sidebar font-bold">HÀNH ĐỘNG</h4>
                    <ul class="list-box-sidebar">
                        <li><span class="font-bold">NHẬP</span> - <span class="font-color-898989">Nhập kho hoặc Bắt đầu vào một khâu</span></li>
                        <li><span class="font-bold">XUẤT</span> - <span class="font-color-898989">Xuất kho hoặc Ra khỏi một khâu</span></li>
                    </ul>
                </div>
            </div>


            <!--box 2-->

            <div class="col-lg-8 h100 pos-relative" id="_result_export">
                <div class="primary background-white">
                    <h4 class="title-column text-uppercase font-bold">Kết quả quét</h4>
                    <div class="box-primary pos-relative">
                        <ul class="pull-left list-box-primary">
                            <li v-repeat=" dataWarehouses" class="item-box-primary">
                                <div class="pull-left box-left-primary">@{{ barcode_length }}</div>
                                <div class="pull-right box-right-primary">
                                    <div class="pull-left title-box-primary">
                                        <div class="code text-uppercase font-bold"><span>@{{ barcode }}</span></div>
                                        <div class="store text-uppercase" v-if="action_warehouse == 'IN'"> NHẬP KHO / @{{ current_warehouse }}</div>
                                        <div class="store text-uppercase" v-if="action_warehouse == 'OUT'"> XUẤT KHO / @{{ current_warehouse }}</div>
                                    </div>
                                    <div class="pull-right des-box-primary text-right">
                                        <div class="link-box-primary">
                                            <a target="_blank" class="" href="" title="">nhatminh247.com</a>
                                        </div>
                                        <div class="time-box-primary">@{{ time }}</div>
                                    </div>
                                </div>
                            </li>

                            {{--<li class="item-box-primary border-radius-red">--}}
                                {{--<div class="pull-left box-left-primary">1</div>--}}
                                {{--<div class="pull-right box-right-primary">--}}
                                    {{--<div class="pull-left title-box-primary">--}}
                                        {{--<div class="code text-uppercase font-bold"><span>S/HI12_2443646</span></div>--}}
                                        {{--<div class="store text-uppercase">NHẬP / VNHN</div>--}}
                                    {{--</div>--}}

                                    {{--<div class="pull-right des-box-primary text-right">--}}
                                        {{--<div class="font-color-ec0423 text-center">--}}
                                            {{--<i class="fa fa-warning padding-top-9"></i><br />--}}
                                            {{--Lỗi dữ liệu, vui lòng thử lại--}}
                                        {{--</div>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                            {{--</li>--}}

                        </ul>

                        <div class="pos-absolute box-primary-line"></div>

                        <div class="box-footer-primary pos-absolute">
                            <ul class="list-item-footer-primary">
                                <li class="font-bold cursor-pointer export-excel"><i class="fa fa-file-excel-o"></i>XUẤT EXCEL</li>
                                <li class="font-bold cursor-pointer clear-result"><i class="fa fa-times"></i>XÓA KQ QUÉT</li>
                                <li class="link-popup">
                                    <button class="btn btn-blue " data-toggle="modal" data-target="#popupbarcode" type="button"><span class="uppercase font-white font-bold">Đóng bao</span></button>
                                </li>
                            </ul>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
    <!--end html in here-->



@endsection


@section('css_bottom')
    @parent
    <link rel="stylesheet" type="text/css" href="{{ asset('css/warehouse.css') }}">
@endsection

@section('js_bottom')
    @parent
    <script type="text/javascript" src="{{ asset('js/jquery.slimscroll.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/vue.js') }}"></script>

    <script type="text/javascript" src="{{ asset('js/process/warehouse/home.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/process/warehouse/logistic-frontend.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/process/warehouse/menu.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/process/warehouse/action_warehouse.js') }}"></script>

    {{--<script>--}}
        {{--$(function(){--}}
            {{--$('.list-box-primary').slimScroll({--}}
                {{--height: '200px'--}}
            {{--});--}}
        {{--});--}}

    {{--</script>--}}

@endsection

