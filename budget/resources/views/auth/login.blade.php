@extends('layouts.loginlayout')

@section('content')
<div class="container" style="display:flex;justify-content:center;align-items:center;height:100%;">
    <!--<div class="row">-->
        <!--<div class="col-md-8">-->
            <!--<div class="card">-->
                <!--<div class="card-header">{{ __('Login') }}</div>-->
    <!--<div style="width:500px;height:300px;">
    <form method="POST" action="{{ route('login') }}">
        <div>
            <div>
                {{-- エラーメッセージ --}}
                @if ($errors->any())
                <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                </div>
                @endif
            </div>
            <div class="loginColumn">
                <label for="userid" >{{ __('ユーザーID') }}</label>
                <input id="userid" type="text" class="" name="LoginAccount" value="{{ old('LoginAccount') }}" required autofocus>
            </div>
            <div>
                <label for="password" >{{ __('パスワード') }}</label>
                <input id="password" type="password" class="@error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
            </div>
            <div>
                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                <label class="form-check-label" for="remember">
                    {{ __('Remember Me') }}
                </label>
            </div>
            <button type="submit" class="btn btn-primary">
                {{ __('Login') }}
            </button>
            @if (Route::has('password.request'))
                <a class="btn" href="{{ route('password.request') }}">
                    {{ __('パスワードを忘れた場合') }}
                </a>
            @endif

        </div>
    </form>
    </div>-->
                <div class="login-area-body">
                    <div class="loginTitle">{{ config('app.name', '予算管理支援システム') }}</div>
                    <div class="loginSubTitle">{{ config('app.organization', '') }} {{ config('app.department', '') }}</div>
                    <form id="frmLogin" method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="form-group">
                            <label for="userid" class="text-md-right">{{ __('ユーザーID') }}</label>

                            <div>
                                <input id="userid" type="text" class="form-control" style="width:200px;" name="LoginAccount" value="{{ old('LoginAccount') }}" required autofocus>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="password" class="text-md-right">{{ __('パスワード') }}</label>

                            <div >
                                <input id="password" type="password" style="width:200px;" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                            </div>
                        </div>

                        <div class="form-group remember">
                            <div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                            </div>
                            <div class="forgetlink">
                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" >
                                        {{ __('パスワードを忘れた場合') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <div>
                                {{-- エラーメッセージ --}}
                                @if ($errors->any())
                                <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                </div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="wrapperCircleLoginButton">
                            <button type="submit" class="circleLoginButton">
                                <span class="fa fa-3x fa-sign-in"></span>
                            </button>
                        </div>

                    </form>
                </div>
            <!--</div>-->
        <!--</div>-->
    <!--</div>-->
</div>
<script>


</script>
@endsection
