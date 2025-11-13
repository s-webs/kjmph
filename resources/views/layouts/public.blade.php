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
    <link rel="apple-touch-icon" sizes="180x180" href="/assets/img/favicon-180.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/assets/img/favicon-32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/assets/img/favicon-16.png">
    @vite('resources/css/app.css')
    <title>{{ $settings->joournal_name }}</title>
</head>
<body>
<style>[x-cloak] {
        display: none !important;
    }</style>
@stack('styles')
@include('layouts.components.header')
<div class="container mx-auto px-4 mt-[30px] flex flex-col lg:flex-row items-stretch">
    <main class="flex-1">@yield('content')</main>

    <div class="w-[3px] shrink-0 mx-[30px] bg-custom-main self-stretch"></div>
    <aside class="w-full lg:w-[250px] shrink-0 mt-[30px] lg:mt-[0]">
        @include('layouts.components.aside')
    </aside>
</div>
@include('layouts.components.footer')
@vite('resources/js/app.js')
@stack('scripts')
<footer>

</footer>
</body>
</html>
