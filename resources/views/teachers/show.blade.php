@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold">{{ $teacher->name }}</h1>
        <p class="text-slate-400">Detalhes do professor.</p>
    </div>

    <div class="max-w-2xl space-y-2 break-words rounded border border-slate-800 bg-slate-900 p-6">
        <p><strong>Matrícula:</strong> {{ $teacher->registration }}</p>
        <p><strong>Nome:</strong> {{ $teacher->name }}</p>
        <p><strong>E-mail:</strong> {{ $teacher->email ?? 'Não informado' }}</p>
        <p><strong>Unidade:</strong> {{ $teacher->schoolUnit->name }}</p>
        <p><strong>Máximo semanal:</strong> {{ $teacher->max_weekly_periods }} períodos</p>
        <p><strong>Máximo diário:</strong> {{ $teacher->max_daily_periods }} períodos</p>
        <p>
            <strong>Status:</strong>
            {{ $teacher->is_active ? 'Ativo' : 'Inativo' }}
        </p>

        <div class="pt-4">
            <strong>Especialidades:</strong>

            @if ($teacher->specialties->isEmpty())
                <p class="text-slate-400 mt-2">Nenhuma especialidade vinculada.</p>
            @else
                <ul class="list-disc list-inside mt-2">
                    @foreach ($teacher->specialties as $specialty)
                        <li>
                            {{ $specialty->name }}
                            <span class="text-slate-400">
                                — aderência: {{ $specialty->pivot->adherence_score }}%
                            </span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:flex-wrap">
        <a href="{{ route('teachers.specialties.index', $teacher) }}"
        class="bg-blue-600 hover:bg-blue-500 px-4 py-2 rounded">
            Gerenciar especialidades
        </a>

        <a
            href="{{ route('teachers.availability.edit', $teacher) }}"
            class="bg-emerald-600 hover:bg-emerald-500 px-4 py-2 rounded"
        >
            Gerenciar disponibilidade
        </a>

        <a href="{{ route('teachers.edit', $teacher) }}" class="bg-yellow-600 hover:bg-yellow-500 px-4 py-2 rounded">
            Editar
        </a>

        @if ($teacher->is_active)
            <form action="{{ route('teachers.deactivate', $teacher) }}" method="POST" class="inline">
                @csrf
                @method('PATCH')

                <button
                    type="submit"
                    class="bg-red-600 hover:bg-red-500 px-4 py-2 rounded"
                    onclick="return confirm('Inativar este professor? Ele não será considerado em novas alocações.')"
                >
                    Inativar
                </button>
            </form>
        @endif

        <a href="{{ route('teachers.index') }}" class="px-4 py-2 rounded bg-slate-800">
            Voltar
        </a>
    </div>
@endsection
