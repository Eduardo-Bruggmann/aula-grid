<nav class="grid w-full grid-cols-2 gap-x-4 gap-y-3 text-sm sm:flex sm:flex-wrap sm:justify-end lg:w-auto" aria-label="Navegação principal">
    <a
        href="{{ route('school-units.index') }}"
        @class([
            'transition hover:text-white',
            'text-blue-400' => request()->routeIs('school-units.*'),
            'text-slate-300' => ! request()->routeIs('school-units.*'),
        ])
        @if (request()->routeIs('school-units.*')) aria-current="page" @endif
    >
        Unidades
    </a>
    <a
        href="{{ route('specialties.index') }}"
        @class([
            'transition hover:text-white',
            'text-blue-400' => request()->routeIs('specialties.*'),
            'text-slate-300' => ! request()->routeIs('specialties.*'),
        ])
        @if (request()->routeIs('specialties.*')) aria-current="page" @endif
    >
        Especialidades
    </a>
    <a
        href="{{ route('subjects.index') }}"
        @class([
            'transition hover:text-white',
            'text-blue-400' => request()->routeIs('subjects.*'),
            'text-slate-300' => ! request()->routeIs('subjects.*'),
        ])
        @if (request()->routeIs('subjects.*')) aria-current="page" @endif
    >
        Unidades Curriculares
    </a>
    <a
        href="{{ route('teachers.index') }}"
        @class([
            'transition hover:text-white',
            'text-blue-400' => request()->routeIs('teachers.*'),
            'text-slate-300' => ! request()->routeIs('teachers.*'),
        ])
        @if (request()->routeIs('teachers.*')) aria-current="page" @endif
    >
        Professores
    </a>
    <a
        href="{{ route('school-classes.index') }}"
        @class([
            'transition hover:text-white',
            'text-blue-400' => request()->routeIs('school-classes.*'),
            'text-slate-300' => ! request()->routeIs('school-classes.*'),
        ])
        @if (request()->routeIs('school-classes.*')) aria-current="page" @endif
    >
        Turmas
    </a>
    <a
        href="{{ route('allocation-runs.index') }}"
        @class([
            'transition hover:text-white',
            'text-blue-400' => request()->routeIs('allocation-runs.*'),
            'text-slate-300' => ! request()->routeIs('allocation-runs.*'),
        ])
        @if (request()->routeIs('allocation-runs.*')) aria-current="page" @endif
    >
        Alocações
    </a>
</nav>
