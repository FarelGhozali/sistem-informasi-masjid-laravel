<!DOCTYPE HTML>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Informasi Masjid')</title>
    <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/fontawesome-all.min.css') }}">
    <noscript><link rel="stylesheet" href="{{ asset('assets/css/noscript.css') }}"></noscript>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    <style>
        #bg:after {
            background-image: url('https://images.unsplash.com/photo-1547036967-23d11aacaee0?w=1920&auto=format&fit=crop') !important;
        }
    </style>
</head>
<body class="is-preload">
    <div id="wrapper">
        @yield('header')
        <div id="main">
            @yield('content')
        </div>
        @yield('footer')
    </div>

    <!-- BG -->
    <div id="bg"></div>

    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/browser.min.js') }}"></script>
    <script src="{{ asset('assets/js/breakpoints.min.js') }}"></script>
    <script src="{{ asset('assets/js/util.js') }}"></script>
    <script src="{{ asset('assets/js/main.js') }}"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('scripts')
</body>
</html>