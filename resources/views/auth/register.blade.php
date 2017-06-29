@extends('layouts.landing')

@section('content')


    <div class="form-suggestion">
        Đăng ký tài khoản miễn phí.
    </div>

    <form class="form-horizontal" role="form" method="POST" action="{{ route('register') }}">

        <input type="hidden" name="user_refer" value="{{@$user_refer}}">

        {{ csrf_field() }}

        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">

            <div class="col-md-12">
                <input placeholder="Họ & Tên" id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required autofocus>

                @if ($errors->has('name'))
                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                @endif
            </div>
        </div>

        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">

            <div class="col-md-12">
                <input id="email" placeholder="Email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>

                @if ($errors->has('email'))
                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                @endif
            </div>
        </div>

        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">

            <div class="col-md-12">
                <input id="password" placeholder="Mật khẩu" type="password" class="form-control" name="password" required>

                @if ($errors->has('password'))
                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                @endif
            </div>
        </div>

        <div class="form-group">

            <div class="col-md-12">
                <input id="password-confirm" placeholder="Nhập lại mật khẩu" type="password" class="form-control" name="password_confirmation" required>
            </div>
        </div>

        <div class="form-group">
            <div class="col-md-12">
                <button type="submit" class="btn btn-primary">
                    Đăng ký
                </button>

                <a class="btn btn-link " href="{{ route('login') }}">
                    Đăng nhập
                </a>
            </div>
        </div>
    </form>




@endsection
