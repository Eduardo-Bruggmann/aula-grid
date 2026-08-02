<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AulaGrid</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen overflow-x-hidden bg-slate-950 text-slate-100">
    <header class="border-b border-slate-800 bg-slate-900">
        <div class="mx-auto flex max-w-6xl flex-col gap-4 px-4 py-4 sm:px-6 lg:flex-row lg:items-center lg:justify-between">
            <a href="{{ route('dashboard') }}" class="font-bold text-xl">
                AulaGrid
            </a>

            @include('layouts.navigation')
        </div>
    </header>

    <main class="mx-auto min-w-0 max-w-6xl px-4 py-6 sm:px-6 sm:py-8">
        @if (session('success'))
            <div class="mb-6 rounded bg-green-900/40 border border-green-700 px-4 py-3 text-green-200">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 rounded border border-red-700 bg-red-900/40 px-4 py-3 text-red-200">
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>
