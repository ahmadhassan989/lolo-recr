<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ asset('css/careers.css') }}">
    <title>@yield('title', 'Careers at Lolo')</title>
</head>
<body>
<header class="header">
    <h1 class="logo"><a href="{{ route('home') }}">Lolo Recruiting</a></h1>
    <nav>
        <a href="{{ route('home') }}">Careers</a>
    </nav>
</header>
<main class="content">
    @yield('content')
</main>
<footer class="footer">&copy; {{ date('Y') }} Lolo Recruiting System</footer>
</body>
</html>
