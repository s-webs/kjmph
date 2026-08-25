<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    @vite('resources/css/app.css')
    <title>Портал рецензента — {{ config('app.name') }}</title>
</head>
<body class="min-h-screen bg-slate-50 text-slate-900" style="font-family: 'Open Sans', sans-serif;">
<main class="mx-auto max-w-3xl px-4 py-10">
    <header class="mb-8 border-b border-slate-200 pb-4">
        <p class="text-sm uppercase tracking-wide text-custom-main">Портал рецензента</p>
        <h1 class="mt-1 text-2xl font-semibold text-custom-main">{{ config('app.name') }}</h1>
    </header>

    @if (session('success'))
        <div class="mb-6 rounded border border-green-200 bg-green-50 px-4 py-3 text-green-800">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 rounded border border-red-200 bg-red-50 px-4 py-3 text-red-800">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 rounded border border-red-200 bg-red-50 px-4 py-3 text-red-800">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @yield('content')
</main>
</body>
</html>
