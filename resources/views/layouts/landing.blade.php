<!DOCTYPE html>
<html>
<head>
    <title>Page Register</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" type="text/css" href="{{ asset('css/vendor.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/flat-admin.css') }}">


</head>
<body>
<div class="app app-default">

    <div class="app-container app-login">
        <div class="flex-center">
            <div class="app-header"></div>
            <div class="app-body">
                <div class="loader-container text-center">
                    <div class="icon">
                        <div class="sk-folding-cube">
                            <div class="sk-cube1 sk-cube"></div>
                            <div class="sk-cube2 sk-cube"></div>
                            <div class="sk-cube4 sk-cube"></div>
                            <div class="sk-cube3 sk-cube"></div>
                        </div>
                    </div>
                    <div class="title">Logging in...</div>
                </div>
                <div class="app-block">


                    <div class="app-form">
                        <h3><strong class="text-uppercase">NhậtMinh</strong> 247</h3>

                        <br>

                        @yield('content')
                    </div>

                </div>
            </div>
            <div class="app-footer">
            </div>
        </div>
    </div>

</div>

</body>
</html>