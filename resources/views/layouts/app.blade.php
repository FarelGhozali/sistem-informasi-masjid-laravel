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
</head>
<body class="is-preload">

    <!-- Wrapper -->
    <div id="wrapper">

        <!-- Header -->
        <header id="header">
            <div class="logo">
                <span class="icon fa-mosque"></span>
            </div>
            <div class="content">
                <div class="inner">
                    <h1>@yield('header_title', 'Sistem Informasi Masjid')</h1>
                    <p>@yield('header_subtitle', 'Platform informasi masjid terintegrasi')</p>
                </div>
            </div>
            <nav>
                <ul>
                    <li><a href="#intro">Intro</a></li>
                    <li><a href="#about">Tentang</a></li>
                    <li><a href="#contact">Kontak</a></li>
                </ul>
            </nav>
        </header>

        <!-- Main -->
        <div id="main">
            @yield('content')
        </div>

        <!-- Footer -->
        <footer id="footer">
            <p class="copyright">&copy; {{ date('Y') }} Sistem Informasi Masjid. Design: <a href="https://html5up.net">HTML5 UP</a>.</p>
        </footer>

    </div>

    <!-- BG -->
    <div id="bg"></div>

    <!-- Scripts -->
    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/browser.min.js') }}"></script>
    <script src="{{ asset('assets/js/breakpoints.min.js') }}"></script>
    <script src="{{ asset('assets/js/util.js') }}"></script>
    <script src="{{ asset('assets/js/main.js') }}"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('scripts')
</body>
</html>