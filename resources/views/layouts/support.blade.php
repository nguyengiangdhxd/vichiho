
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <meta name="description" content="">
    <meta name="author" content="">
    <title>NhatMinh247 - HoTro</title>

    @include('partials/__facebook_pixel')

    <!-- Bootstrap Core CSS -->
    <link href="{{ asset('css/support/bootstrap.min.css')  }}" rel="stylesheet">

    <link rel="chrome-webstore-item" href="https://chrome.google.com/webstore/detail/inhnmjhbedekbkffcjopnjakeiiclcej">

    <script>
        function ExtInstall() {
            if (chrome.app.isInstalled){
                alert("already installed!");
            }else{
                chrome.webstore.install();
            }
        }
    </script>

    <!-- Temporary fix for navbar responsiveness -->
    <style>
        body {
            padding-top: 100px;
        }

        @media (min-width: 992px) {
            body {
                padding-top: 100px;
            }
        }

        .pagination {
            margin-bottom: 15px;
        }

        .navbar-toggler {
            z-index: 1;
        }

        @media (max-width: 576px) {
            nav > .container {
                width: 100%;
            }
        }
    </style>

</head>

<body>

<!-- Navigation -->
<nav class="navbar fixed-top navbar-toggleable-md navbar-inverse bg-inverse">
    <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarExample" aria-controls="navbarExample" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="container">
        <a class="navbar-brand" href="{{ url('')  }}">
            <img width="50px" class="logo" src="{{ asset('images/home/logo.png')  }}" alt="">
            NhatMinh247
        </a>
        <div class="collapse navbar-collapse" id="navbarExample">
            <ul class="navbar-nav ml-auto">

                <li class="nav-item active">
                    <a class="nav-link" href="{{ url('')  }}">Trang chủ <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item active"><a class="nav-link" href="{{ url('ho-tro', 4)  }}">Hướng dẫn</a></li>
                <li class="nav-item active"><a class="nav-link" href="{{ url('ho-tro', 5)  }}">Nguồn hàng</a></li>
                <li class="nav-item active"><a class="nav-link" href="{{ url('ho-tro', 3)  }}">Biểu phí</a></li>
                <li class="nav-item active"><a class="nav-link" href="{{ url('ho-tro', 1)  }}">Nạp tiền</a></li>

                @if (Route::has('login'))
                    @if (Auth::check())
                        <li class="nav-item active"><a class="nav-link" href="{{ url('/home') }}">Vào hệ thống</a></li>
                    @else
                        <li class="nav-item active"><a class="nav-link" href="{{ url('/register') }}">Tạo tài khoản</a></li>
                        <li class="nav-item active"><a class="nav-link" href="{{ url('/login') }}">Đăng nhập</a></li>
                    @endif
                @endif

                <li>
                    <button class="btn btn-danger" onclick="ExtInstall()" id="install-button">Công cụ</button>
                </li>

            </ul>
        </div>
    </div>
</nav>

<!-- Page Content -->
<div class="container">
    <div class="row">
        <div class="col-sm-8 col-xs-12">
            @yield('content')
        </div>
        <div class="col-sm-4 col-xs-12">
            @yield('sidebar')
        </div>
    </div>

</div>
<!-- /.container -->

<!-- Footer -->
<footer class="py-5 bg-inverse">
    <div class="container">
        <p class="m-0 text-center text-white">Copyright &copy; NhatMinh247 2017</p>
    </div>
    <!-- /.container -->
</footer>

<!-- jQuery Version 3.1.1 -->
<script src="{{ asset('bower_components/jquery/dist/jquery.js')  }}"></script>

<!-- Tether -->
<script src="{{ asset('js/support/tether.min.js')  }}"></script>

<!-- Bootstrap Core JavaScript -->
<script src="{{ asset('bower_components/bootstrap/dist/js/bootstrap.js')  }}"></script>

</body>

</html>
