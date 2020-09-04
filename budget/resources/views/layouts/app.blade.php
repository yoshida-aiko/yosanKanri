<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Budget Management System') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="{{ asset('js/script.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('css/navi.css') }}" rel="stylesheet">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">

</head>
<body>
    <div >
        <nav class="navbar navbar-expand-md navbar-light bg-darkblue shadow-sm" id="myNavbar">
            <div class="container">
                <div style="width:100%;">
                    <a class="navbar-brand" href="{{ url('/') }}">
                        {{ config('app.name', 'Budget Management System') }}
                    </a>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <!-- Left Side Of Navbar -->
                        <ul id="myNavbar" class="navbar-nav mr-auto">
                            <li class="current"><a href="{{ route('home') }}" class="nav-menu-home">トップ</a></li>
                            <li><a href="{{ route('SearchPage.index') }}" class="nav-menu-search">検索</a></li>
                            <li><a href="#" class="nav-menu-orderrequest">発注依頼</a></li>
                            @if (Auth::user()->UserAuth >= 1)
                            <li><a href="#" class="nav-menu-order">発注</a></li>
                            @endif
                            @if (Auth::user()->UserAuth >= 2)
                            <li><a href class="nav-menu-delivery">納品</a></li>
                            @endif
                            @if (Auth::user()->UserAuth >= 32)
                            <li><a href="#" class="nav-menu-payment">支払</a></li>
                            @endif
                            @if (Auth::user()->UserAuth >= 4)
                            <li><a href="#" class="nav-menu-budget">予算状況</a></li>
                            @endif
                            @if (Auth::user()->UserAuth >= 8)
                            <li><a href="#" class="nav-menu-purchase">購入履歴</a></li>
                            @endif
                            @if (Auth::user()->UserAuth >= 16) 
                            <li class="dropdown">
                                <a class="dropdown-toggle nav-menu-master" href="#" data-toggle="dropdown" >マスタ</a>
                                <div class="dropdown-menu dropdown-menu-originalcolor" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('User.index') }}">ユーザー</a>
                                    <a class="dropdown-item" href="{{ route('Supplier.index') }}">発注先</a>
                                    <a class="dropdown-item" href="{{ route('Maker.index') }}">メーカーマスタ</a>
                                    <a class="dropdown-item" href="{{ route('Budget.index') }}">予算マスタ</a>
                                    <a class="dropdown-item" href="#">設定</a>
                                </div>
                            </li>
                            @endif
                        </ul>

                        <!-- Right Side Of Navbar -->
                        <ul class="navbar-login">
                            <!-- Authentication Links -->
                            @guest
                                <!--<li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>-->
                                @if (Route::has('register'))
                                    <!--<li class="nav-item">
                                        <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                    </li> -->
                                @endif
                            @else
                                <li class="nav-item dropdown">
                                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                        {{ Auth::user()->UserNameJp }} <span class="caret"></span>
                                    </a>

                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                        <a class="dropdown-item" href="{{ route('logout') }}"
                                        onclick="event.preventDefault();
                                                        document.getElementById('logout-form').submit();">
                                            {{ __('Logout') }}
                                        </a>
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                            @csrf
                                        </form>
                                    </div>
                                </li>
                            @endguest
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <main>
            @yield('content')
        </main>
    </div>
</body>
</html>
