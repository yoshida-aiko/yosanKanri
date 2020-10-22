@extends('layouts.loginlayout')

@section('content')
<div class="container">
    <div class="row justify-content-center" style="margin-top:30px;">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">パスワードリセットメールを送信します。</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="LoginAccount" class="col-md-4 col-form-label text-md-right">ユーザーID</label>

                            <div class="col-md-6">
                                <input id="LoginAccount" type="LoginAccount" class="form-control @error('email') is-invalid @enderror" name="LoginAccount" value="{{ old('LoginAccount') }}" required autofocus>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">メールアドレス</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    メール送信
                                </button>
                                <a href="{{ route('logout') }}" class="btn btn-secondary" 
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">ログイン画面へもどる</a>
                                
                            </div>
                        </div>
                    </form>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
