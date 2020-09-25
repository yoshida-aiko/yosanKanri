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
    <link href="{{ asset('css/styleNakamichi.css') }}" rel="stylesheet">
    <link href="{{ asset('css/navi.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
    
    <!-- jstree -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.2/jstree.min.js" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.2/themes/default/style.min.css">
    
    <!-- jQuery UI -->
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous" defer></script>

    <!--BlockUI-->
    <script type="text/javascript" src="{{ asset('js/jquery.blockUI.js') }}" defer></script>

    <!--contextmenu-->
    <script type="text/javascript" src="{{ asset('js/popmenu.js') }}" defer></script>
    <link href="{{ asset('css/popmenu.css') }}" rel="stylesheet">

</head>
<body>
    <div >
        <nav class="navbar navbar-expand-md navbar-light bg-darkblue shadow-sm" id="myNavbar">
            <div class="container">
                <div style="width:100%;">
                    <a class="navbar-brand" href="{{ url('/') }}">
                        {{ config('app.name', '予算管理システム') }}
                    </a>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <!-- Left Side Of Navbar -->
                        <ul id="myNavbar" class="navbar-nav mr-auto">
                            <li class="current"><a href="{{ route('home') }}" class="nav-menu-home">トップ</a></li>
                            <li><a href="{{ route('SearchPage.index') }}" class="nav-menu-search">検索</a></li>
                            <li><a href="{{ route('OrderRequest.index') }}" class="nav-menu-orderrequest">発注依頼</a></li>
                            @if (strpos(Auth::user()->UserAuthString,'Order') !== false)
                            <li><a href="{{ route('Order.index') }}" class="nav-menu-order">発注</a></li>
                            @endif
                            @if (strpos(Auth::user()->UserAuthString,'Delivery') !== false)
                            <li><a href class="nav-menu-delivery">納品</a></li>
                            @endif
                            @if (strpos(Auth::user()->UserAuthString,'Payment') !== false)
                            <li><a href="#" class="nav-menu-payment">支払</a></li>
                            @endif
                            @if (strpos(Auth::user()->UserAuthString,'Budget') !== false)
                            <li><a href="#" class="nav-menu-budget">予算状況</a></li>
                            @endif
                            @if (strpos(Auth::user()->UserAuthString,'Purchase') !== false)
                            <li><a href="#" class="nav-menu-purchase">購入履歴</a></li>
                            @endif
                            @if (strpos(Auth::user()->UserAuthString,'Master') !== false) 
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
                            <li class="nav-item dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                {{ Config::get('languages')[App::getLocale()] }}
                            <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                @foreach (Config::get('languages') as $lang => $language)
                                    @if ($lang != App::getLocale())
                                        <li>
                                            <a href="{{ route('lang.switch', $lang) }}">{{$language}}</a>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                            </li>
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
