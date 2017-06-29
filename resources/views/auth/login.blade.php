@extends('layouts.landing')

@section('content')

    <form class="form-horizontal" role="form" method="POST" action="{{ route('login') }}">
        {{ csrf_field() }}

        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">

            <div class="col-md-12">
                <input id="email" type="email" placeholder="Email" class="form-control" name="email" value="{{ old('email') }}" required autofocus>

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
                <div class="checkbox">

                    <input id="chk-remember" type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label for="chk-remember"> Ghi nhớ</label>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="col-md-12">
                <button type="submit" class="btn btn-primary">
                    Đăng nhập
                </button>

                <a class="btn btn-link " href="{{ route('register') }}">
                    Đăng ký tài khoản
                </a>

                <a class="btn btn-link hidden" href="{{ route('password.request') }}">
                    Quên mật khẩu?
                </a>
            </div>
        </div>
</form>

@endsection
