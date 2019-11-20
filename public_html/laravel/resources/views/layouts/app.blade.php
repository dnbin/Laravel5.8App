<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
<header>
    <div class="top-bar">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <nav>
                        <ul>
                            <li>
                                <a href="">United States</a>
                                <ul class="submenu">
                                    <li><a href="">United Kingdom</a></li>
                                    <li><a href="">Spain</a></li>
                                    <li><a href="">China</a></li>
                                </ul>
                            </li>
                            <li>
                                <a href="">USD</a>
                                <ul class="submenu">
                                    <li><a href="">GBP</a></li>
                                    <li><a href="">EUR</a></li>
                                    <li><a href="">CNY</a></li>
                                </ul>
                            </li>
                            <li>
                                <a href="">English</a>
                                <ul class="submenu">
                                    <li><a href="">English UK</a></li>
                                    <li><a href="">Spanish</a></li>
                                    <li><a href="">Chinese</a></li>
                                </ul>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <div class="logo">
        <div class="container">
            <div class="row">
                <div class="col-md-5 left">
                    <ul>
                        <li class="phone"><a href="">747653062-4</a></li>
                        <li class="chat"><a href="">Live Chat</a></li>
                    </ul>
                </div>
                <div class="col-md-2 middle">
                    <a href="{{ url('/') }}" title="{{ config('app.name', 'Laravel') }}"><img src="{{asset('images/logo.png')}}" alt="{{ config('app.name', 'Laravel') }}"></a>
                </div>
                <div class="col-md-5 right">
                    <ul>
                        <li class="wishlist"><a href="">Wish List</a></li>
                        <!-- Authentication Links -->
                        @guest
                            <li class="signin"><a href="{{ route('login') }}">{{ __('Sign In') }}</a></li>
                            @if (Route::has('register'))
                                <li class="signup"><a href="{{ route('register') }}">{{ __('Sign Up') }}</a></li>
                            @endif
                        @else
                            {{--
                            <li class="signin">
                                <a href="{{route('home')}}">
                                    {{ Auth::user()->name }} @if(Auth::user()->hasRole(['admin'])) (admin) @endif <span class="caret"></span>
                                </a>
                            </li>
                            --}}
                            <li class="signin">
                                <a href="{{ route('logout') }}"
                                   onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                    {{ __('Logout') }}
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                            </li>

                            {{--
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }} @if(Auth::user()->hasRole(['admin'])) (admin) @endif <span class="caret"></span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('home') }}">Dashboard</a>

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
                            --}}
                        @endguest
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="navigation">
        <div class="container">
            <div class="row">
                <div class="col-md-1 hamburger">
                    <img src="{{asset('images/hamburger-menu.png')}}" alt="">
                </div>
                <div class="col-md-10">
                    <nav>
                        <ul>
                            <li><a href="{{url('/')}}">Home</a></li>
                            <li><a href="">How It Works</a></li>
                            <li><a href="">About Us</a></li>
                            @auth
                            <li><a href="{{route('home')}}">My Account @if(Auth::user()->hasRole(['admin'])) (admin) @endif</a></li>
                                @else
                                <li><a href="{{route('home')}}">My Account</a></li>
                            @endauth
                        </ul>
                    </nav>
                </div>
                <div class="col-md-1 search">
                    <img src="{{asset('images/search.png')}}" alt="">
                </div>
            </div>
        </div>
    </div>
</header>
    @include('layouts.spinner')
    @yield('content')

<footer>
    <div class="container">
        <div class="row">
            <div class="col-md-2 logo">
                <a href="{{ url('/') }}" title="{{ config('app.name', 'Laravel') }}"><img src="{{asset('images/logo.png')}}" alt="{{ config('app.name', 'Laravel') }}"></a>
            </div>
            <div class="col-md-8 navigation">
                <ul>
                    <li><a href="">About Us</a></li>
                    <li><a href="">Careers</a></li>
                    <li><a href="">FAQ</a></li>
                    <li><a href="">Blog</a></li>
                    <li><a href="">Contacts</a></li>
                </ul>
            </div>
            <div class="col-md-2 social">
                <ul>
                    <li><a href=""><i class="fab fa-facebook-f"></i></a></li>
                    <li><a href=""><i class="fab fa-twitter"></i></a></li>
                    <li><a href=""><i class="fab fa-instagram"></i></a></li>
                </ul>
            </div>
            <p class="copyright">© 2019 Company. All rights reserved</p>
        </div>
    </div>
</footer>

    <!-- Scripts -->
    <script src="{{ asset('js/manifest.js') }}"></script>
    <script src="{{ asset('js/vendor.js') }}"></script>
    <script src="{{ asset('js/app.js') }}"></script>

</body>
</html>
