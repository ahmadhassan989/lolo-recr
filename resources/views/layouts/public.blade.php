<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ asset('css/careers.css') }}">
    <title>@yield('title', 'Careers') · {{ config('app.name') }}</title>
</head>
<body>
<header class="site-header">
    <div class="header-inner">
        <a href="{{ route('home') }}" class="brand">
            <span class="brand-mark">{{ strtoupper(substr(config('app.name'), 0, 1)) }}</span>
            <span class="brand-name">{{ config('app.name') }}</span>
        </a>
        <nav class="header-nav">
            <a href="{{ route('home') }}">Open Positions</a>
            @auth
                <a href="{{ route('profile.edit') }}">Profile</a>
                <form method="POST" action="{{ route('logout') }}" class="logout-form">
                    @csrf
                    <button type="submit" class="nav-cta">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="nav-cta">Team Login</a>
            @endauth
        </nav>
    </div>
</header>
<main class="page">
    @yield('content')
</main>
<footer class="site-footer">
    <span>&copy; {{ date('Y') }} {{ config('app.name') }}</span>
    <span class="footer-note">All applications are reviewed by our hiring team.</span>
</footer>
</body>
</html>
