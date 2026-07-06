<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AulaGrid</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen">
    <header class="border-b border-slate-800 bg-slate-900">
        <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
            <a href="{{ route('school-units.index') }}" class="font-bold text-xl">
                AulaGrid
            </a>

            <nav class="flex gap-4 text-sm">
                <a href="{{ route('school-units.index') }}" class="text-slate-300 hover:text-white">
                    Unidades
                </a>
                <a href="{{ route('specialties.index') }}" class="text-slate-300 hover:text-white">
                    Especialidades
                </a>
                <a href="{{ route('subjects.index') }}" class="text-slate-300 hover:text-white">
                    Unidades Curriculares
                </a>
                <a href="{{ route('teachers.index') }}" class="text-slate-300 hover:text-white">
                    Professores
                </a>
                <a href="{{ route('school-classes.index') }}" class="text-slate-300 hover:text-white">
                    Turmas
                </a>
            </nav>
        </div>
    </header>

    <main class="max-w-6xl mx-auto px-6 py-8">
        @if (session('success'))
            <div class="mb-6 rounded bg-green-900/40 border border-green-700 px-4 py-3 text-green-200">
                {{ session('success') }}
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>