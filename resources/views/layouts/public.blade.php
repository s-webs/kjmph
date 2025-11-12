<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap"
          rel="stylesheet">
    <link
        rel="stylesheet"
        type="text/css"
        href="https://cdn.jsdelivr.net/npm/@phosphor-icons/web@2.1.2/src/regular/style.css"
    />
    @vite('resources/css/app.css')
    <title>{{ $settings->joournal_name }}</title>
</head>
<body>
@stack('styles')
@include('pages.home.components.header')
<div class="container mx-auto px-4 mt-[30px] flex items-stretch"> <!-- или просто .flex -->
    <main class="flex-1">@yield('content')</main>

    <div class="w-[3px] shrink-0 mx-[30px] bg-custom-main self-stretch"></div>

    <aside class="w-[300px] shrink-0 bg-red-300">
        INDEXES
    </aside>
</div>
@vite('resources/js/app.js')
@stack('scripts')
</body>
</html>
