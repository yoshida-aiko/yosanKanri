<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Budget Management System') }}</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="favicon.ico">
    
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
    <script src="{{ asset('js/jstree.min.js') }}" defer></script>
    <link href="{{ asset('css/jstree/style.min.css') }}" rel="stylesheet">
    
    <!-- jQuery UI -->
     <script src="{{ asset('js/jquery-ui.js') }}" defer></script>
     <link href="{{ asset('css/jquery-ui.css') }}" rel="stylesheet">
     <link href="{{ asset('css/jquery-ui.structure.css') }}" rel="stylesheet">
     <link href="{{ asset('css/jquery-ui.theme.css') }}" rel="stylesheet">

    <!--BlockUI-->
    <script type="text/javascript" src="{{ asset('js/jquery.blockUI.js') }}" defer></script>
    
    <!--datepicker-->
    <script src="{{ asset('js/datepicker-ja.js') }}" defer></script>
    
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
                            <li class="nav-home"><a href="{{ route('home') }}" class="nav-menu-home">{{ __('screenwords.top') }}</a></li>
                            <li class="nav-seacrch"><a href="{{ route('SearchPage.index') }}" class="nav-menu-search">{{ __('screenwords.search') }}</a></li>
                            <li class="nav-orderRequest"><a href="{{ route('OrderRequest.index') }}" class="nav-menu-orderrequest">{{ __('screenwords.orderRequest') }}</a></li>
                            @if (strpos(strtolower(Auth::user()->UserAuthString),'order') !== false)
                            <li class="nav-order"><a href="{{ route('Order.index') }}" class="nav-menu-order">{{ __('screenwords.order') }}</a></li>
                            @endif
                            @if (strpos(strtolower(Auth::user()->UserAuthString),'delivery') !== false)
                            <li class="nav-delivery"><a href="{{ route('Delivery.index') }}" class="nav-menu-delivery">{{ __('screenwords.delivery') }}</a></li>
                            @endif
                            @if (strpos(strtolower(Auth::user()->UserAuthString),'payment') !== false)
                            <!--<li class="nav-payment"><a href="#" class="nav-menu-payment">{{ __('screenwords.payment') }}</a></li>-->
                            @endif
                            @if (strpos(strtolower(Auth::user()->UserAuthString),'budget') !== false)
                            <li class="nav-budgetStatus"><a href="{{ route('BudgetStatus.index') }}" class="nav-menu-budget">{{ __('screenwords.budgetStatus') }}</a></li>
                            @endif
                            @if (strpos(strtolower(Auth::user()->UserAuthString),'purchase') !== false)
                            <li class="nav-purchase"><a href="{{ route('Purchase.index') }}" class="nav-menu-purchase">{{ __('screenwords.buyingHistory') }}</a></li>
                            @endif
                           
                            <li class="nav-master dropdown">
                                <a class="dropdown-toggle nav-menu-master" href="#" data-toggle="dropdown" >{{ __('screenwords.master') }}</a>
                                <div class="dropdown-menu dropdown-menu-originalcolor" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('User.index') }}">{{ __('screenwords.master_user') }}</a>
                                    @if (strpos(strtolower(Auth::user()->UserAuthString),'master') !== false) 
                                    <a class="dropdown-item" href="{{ route('Supplier.index') }}">{{ __('screenwords.master_supplier') }}</a>
                                    <a class="dropdown-item" href="{{ route('Maker.index') }}">{{ __('screenwords.master_maker') }}</a>
                                    <a class="dropdown-item" href="{{ route('Budget.index') }}">{{ __('screenwords.master_budget') }}</a>
                                    <a class="dropdown-item" href="{{ route('Condition.index') }}">{{ __('screenwords.master_setting') }}</a>
                                    @endif
                                </div>
                            </li>
                            
                        </ul>
                        <div class="navbar-login navbar-top navbar-language" @if (config('app.bilingual')!='true') style="display:none;" @endif>
                            <input type="radio" id="rdoLang_Ja" name="rdoLanguage" value="ja" onclick="location.href='{{ route('lang.switch', 'ja') }}'"  @if(App::getLocale()=='ja') checked='checked' @endif ><label for="rdoLang_Ja">日本語</label>
                            <input type="radio" id="rdoLang_En" name="rdoLanguage" value="en" onclick="location.href='{{ route('lang.switch', 'en') }}'"  @if(App::getLocale()=='en') checked='checked' @endif ><label for="rdoLang_En">English</label>
                        </div>
                        <div class="navbar-login navbar-top navbar-username">
                            <?php
                                $UserName = "";
                                if (App::getLocale()=='en' && Auth::user()->UserNameEn!=null) {
                                    $UserName = Auth::user()->UserNameEn;
                                }
                                else {
                                    $UserName = Auth::user()->UserNameJp;
                                }
                            ?>
                            <p title="{{$UserName}}">{{$UserName}}</p>
                        </div>
                        <div class="navbar-login navbar-top navbar-logout">
                            <a href="{{ route('user.logout') }}" class="nav-menu-logout" onclick="setLogout();" >{{ __('Logout') }}</a>
                            
                            <!--<a href="{{ route('logout') }}"  class="nav-menu-logout" onclick="event.preventDefault();setLogout();">
                                {{ __('Logout') }}
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>-->
                        </div>
                        <form id="frmHeaderProductSearch" action="{{url('/SearchPage')}}" method="GET">
                            <div id="headerProductSearchArea">
                                <input type="text" name="txtHeaderProductSearch" class="txtHeaderProductSearch" placeholder="{{ __('screenwords.searchHeaderPlaceholder') }}">
                                <input type="submit" class="btn btn-width70 btn-primary" value="{{ __('screenwords.search') }}">
                            </div>
                        </form>
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
