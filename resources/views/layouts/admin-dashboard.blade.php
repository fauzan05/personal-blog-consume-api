<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{asset('bootstrap-5.3.2-dist/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('fontawesome-free-6.4.2-web/css/all.css')}}">
    <link rel="stylesheet" href="{{asset('css/style.css')}}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.ckeditor.com/ckeditor5/40.2.0/classic/ckeditor.js"></script>
    <title>@yield('title')</title>
</head>

<body class="{{(boolean)Cookie::get('dark-mode') ? "dark" : ""}}">
    @include('layouts.dashboard.header')
    @yield('content')
    @include('layouts.dashboard.footer')
</body>
</html>