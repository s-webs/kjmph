<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @vite('resources/css/app.css')
    <title>Kazakhstanian journal of medicine and pharmacy</title>
</head>
<body>
@stack('styles')
<header class="container mx-auto px-4 bg-custom-main">
    <div>
        <div>

        </div>
        <div>AUTH</div>
    </div>
</header>
@yield('content')
@vite('resources/js/app.js')
@stack('scripts')
</body>
</html>
