<?php
//die(phpinfo());

?>

<!DOCTYPE html>
<html>
<head>
    <title>NhatMinh247 - @yield('page_title')</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    @include('partials/__facebook_pixel')

    @section('css_top')

    <link rel="stylesheet" type="text/css" href="{{ asset('css/vendor.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/flat-admin.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/nprogress.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/custom.css') }}">
    @show

    @include('footer_var_view')

    @section('js_top')
    @show
</head>
<body>

<div class="app app-default">

    <aside class="app-sidebar" id="sidebar">


        <div class="sidebar-header">
            <a class="sidebar-brand" href="{{ url('')  }}"><span class="highlight">NhậtMinh</span> 247</a>
            <button type="button" class="sidebar-toggle">
                <i class="fa fa-times"></i>
            </button>
        </div>

        @include('layouts/sidebar-menu')

    </aside>

    <script type="text/ng-template" id="sidebar-dropdown.tpl.html">
        <div class="dropdown-background">
            <div class="bg"></div>
        </div>
        <div class="dropdown-container">

        </div>
    </script>
    <div class="app-container">

        <nav class="navbar navbar-default" id="navbar">
            <div class="container-fluid">
                <div class="navbar-collapse collapse in">
                    <ul class="nav navbar-nav navbar-mobile">
                        <li>
                            <button type="button" class="sidebar-toggle">
                                <i class="fa fa-bars"></i>
                            </button>
                        </li>
                        <li class="logo">
                            <a class="navbar-brand" href="#"><span class="highlight">NhatMinh</span> 247</a>
                        </li>
                        <li>
                            <button type="button" class="navbar-toggle">
                                <img class="profile-img" src="{{ asset('images/home/_logo.png')  }}">
                            </button>
                        </li>
                    </ul>
                    <ul class="nav navbar-nav navbar-left">
                        <li class="navbar-title">
                            Tỉ giá: {{ number_format(App\Exchange::getExchange(), 0, ",", ".")  }} <sup>đ</sup>&nbsp;&nbsp;&nbsp;Số dư: <span class="text-danger"><?php echo App\Util::formatNumber(Auth::user()->account_balance) ?> <sup>đ</sup></span>

                        </li>
                        {{--<li class="navbar-search hidden-sm">--}}
                            {{--<input id="search" type="text" placeholder="Search.." autocomplete="off">--}}
                            {{--<button class="btn-search"><i class="fa fa-search"></i></button>--}}
                        {{--</li>--}}

                    </ul>

                    <ul class="nav navbar-nav navbar-right">
                        <li class="arrow-none dropdown notification">
                            <a href="{{ url('gio-hang') }}" class="dropdown-toggle" data-toggle="dropdown1111">
                                <div class="icon"><i class="fa fa-shopping-basket" aria-hidden="true"></i></div>
                                <div class="title">New Orders</div>
                                <div class="count width-auto">
                                    {{ App\Cart::getCartTotalQuantityItem(Auth::user()->id)  }}
                                </div>
                            </a>
                        </li>

                        {{--<li class="arrow-none dropdown notification danger">--}}
                            {{--<a href="{{ url('thong-bao') }}" class="dropdown-toggle" data-toggle="dropdown2222">--}}
                                {{--<div class="icon"><i class="fa fa-bell" aria-hidden="true"></i></div>--}}
                                {{--<div class="title">System Notifications</div>--}}
                                {{--<div class="count width-auto">--}}
                                    {{--0--}}
                                {{--</div>--}}
                            {{--</a>--}}
                        {{--</li>--}}

                        {{--<li class="dropdown notification danger">--}}
                            {{--<a href="#" class="dropdown-toggle" data-toggle="dropdown">--}}
                                {{--<div class="icon"><i class="fa fa-bell" aria-hidden="true"></i></div>--}}
                                {{--<div class="title">System Notifications</div>--}}
                                {{--<div class="count width-auto">10</div>--}}
                            {{--</a>--}}
                            {{--<div class="dropdown-menu">--}}
                                {{--<ul>--}}
                                    {{--<li class="dropdown-header">Notification</li>--}}
                                    {{--<li>--}}
                                        {{--<a href="#">--}}
                                            {{--<span class="badge badge-danger pull-right">8</span>--}}
                                            {{--<div class="message">--}}
                                                {{--<div class="content">--}}
                                                    {{--<div class="title">New Order</div>--}}
                                                    {{--<div class="description">$400 total</div>--}}
                                                {{--</div>--}}
                                            {{--</div>--}}
                                        {{--</a>--}}
                                    {{--</li>--}}
                                    {{--<li>--}}
                                        {{--<a href="#">--}}
                                            {{--<span class="badge badge-danger pull-right">14</span>--}}
                                            {{--Inbox--}}
                                        {{--</a>--}}
                                    {{--</li>--}}
                                    {{--<li>--}}
                                        {{--<a href="#">--}}
                                            {{--<span class="badge badge-danger pull-right">5</span>--}}
                                            {{--Issues Report--}}
                                        {{--</a>--}}
                                    {{--</li>--}}
                                    {{--<li class="dropdown-footer">--}}
                                        {{--<a href="#">View All <i class="fa fa-angle-right" aria-hidden="true"></i></a>--}}
                                    {{--</li>--}}
                                {{--</ul>--}}
                            {{--</div>--}}
                        {{--</li>--}}

                        <li class="dropdown profile">
                            <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown">
                                <img class="profile-img" src="{{ asset('images/home/_logo.png')  }}">
                                <div class="title">Profile</div>
                            </a>
                            <div class="dropdown-menu">
                                <div class="profile-info">
                                    <h4 class="username">
                                        <?php
                                            $current_user = App\User::find(Auth::user()->id);
                                        ?>

                                        <span style="@if(Auth::user()->section == App\User::SECTION_CRANE)color: orangered;@endif">{{ Auth::user()->name }}</span>
                                        ({{ $current_user->code }})
                                    </h4>
                                </div>
                                <ul class="action">
                                    <li>
                                        <a href="{{ url('san-pham-da-luu')  }}">Sản phẩm đã lưu</a>
                                    </li>
                                    <li>


                                        @if(Auth::user()->section == App\User::SECTION_CRANE)
                                             <a href="{{ url('user/detail', Auth::user()->id)  }}">
                                        @else
                                            <a href="{{ url('nhan-vien', Auth::user()->id)  }}">
                                        @endif
                                            Thông tin cá nhân
                                        </a>
                                    </li>

                                    <li>
                                        <a href="{{ route('logout') }}"
                                           onclick="event.preventDefault();
                                                document.getElementById('logout-form').submit();">
                                            Thoát
                                        </a>

                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                            {{ csrf_field() }}
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        {{--@include('layouts/help-actions')--}}

        <div class="row">
            <div class="col-xs-12" id="_content">
                @yield('content')
            </div>
        </div>

        @include('layouts/footer')

    </div>

</div>

@yield('widget')

@section('css_bottom')
@show

@section('js_bottom')
<script type="text/javascript" src="{{ asset('js/jquery-3.1.1.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/bootstrap.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/bootbox.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/autoNumeric.min.js')  }}"></script>
<script type="text/javascript" src="{{ asset('js/nprogress.js')  }}"></script>
<script type="text/javascript" src="{{ asset('js/underscore-min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/common.js') }}"></script>
@show

</body>
</html>