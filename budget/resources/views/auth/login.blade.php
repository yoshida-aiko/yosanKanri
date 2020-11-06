@extends('layouts.loginlayout')

@section('content')
<div class="container" style="display:flex;justify-content:center;align-items:center;height:100%;">
    <div class="login-area-body" style="background-color:RGBA(255,255,255,0.2)">
        <div class="loginTitle">{{ config('app.name', '予算管理支援システム') }}</div>
        <div class="loginSubTitle">{{ config('app.organization', '') }} {{ config('app.department', '') }}</div>
        
        <form id="frmLogin" method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-group">
                <input id="userid" type="text" class="form-control" placeholder="User ID" style="margin:0 auto;width:300px;" name="LoginAccount" value="{{ old('LoginAccount') }}" required autofocus>
            </div>

            <div class="form-group">
                <input id="password" type="password" placeholder="Password" style="margin:0 auto;width:300px;" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
            </div>

            <div class="form-group" >
                <button type="submit" style="background-color:#f36963;margin:0 auto;width:300px;height:37px;color:#154170;border-radius:5px;" >Login</button>
            </div>

            <div class="form-group">
                <div class="form-check" style="margin-left:280px">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label" for="remember">
                        {{ __('Remember Me') }}
                    </label>
                </div>
            </div>

            <div class="form-group">
                <div class="forgetlink" style="margin-left:100px;">
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" >
                            {{ __('パスワードを忘れた場合') }}
                        </a>
                    @endif
                </div>
            </div>

            <!--<div class="form-group remember">
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
            </div>-->
            <div class="form-group">
                <div style="margin: 0 auto;">
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
            
            <!--<div class="wrapperCircleLoginButton">
                <button type="submit" class="transparentButton">
                    <span class="fa fa-3x fa-sign-in"></span>
                </button>
            </div>-->

        </form>
    </div>
</div>
@endsection
