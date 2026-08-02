@extends('layouts.app')

@section('content')
    <div class="mb-8">
        <h1 class="text-2xl font-bold">Dashboard</h1>
        <p class="text-slate-400">Visão operacional da base de professores, turmas e unidades.</p>
    </div>

    <section class="mb-8 rounded border border-slate-800 bg-slate-900 p-4 sm:p-6">
        <div class="flex flex-col gap-5 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <h3 class="text-lg font-semibold text-white">
                    Última geração
                </h3>

                @if ($latestAllocationRun)
                    <p class="mt-1 text-sm text-slate-400">
                        Execução #{{ $latestAllocationRun->id }}
                    </p>
                @else
                    <p class="mt-1 text-sm text-slate-400">
                        Nenhuma geração realizada.
                    </p>
                @endif
            </div>

            @if ($latestAllocationRun)
                @php
                    $statusValue = $latestAllocationRun->status instanceof \BackedEnum
                        ? $latestAllocationRun->status->value
                        : $latestAllocationRun->status;

                    $statusLabel = match ($statusValue) {
                        'completed' => 'Concluída',
                        'completed_with_conflicts' => 'Com conflitos',
                        'running' => 'Em andamento',
                        'failed' => 'Falhou',
                        default => ucfirst(str_replace('_', ' ', $statusValue)),
                    };

                    $statusClasses = match ($statusValue) {
                        'completed' => 'border-emerald-500/30 bg-emerald-500/10 text-emerald-300',
                        'completed_with_conflicts' => 'border-amber-500/30 bg-amber-500/10 text-amber-300',
                        'running' => 'border-blue-500/30 bg-blue-500/10 text-blue-300',
                        'failed' => 'border-red-500/30 bg-red-500/10 text-red-300',
                        default => 'border-slate-600 bg-slate-800 text-slate-300',
                    };

                    $filledPeriods = $latestAllocationRun->total_allocations;
                    $totalPeriods = $filledPeriods + $latestAllocationRun->total_conflicts;
                    $hasUsefulResult = $statusValue !== 'failed' || $totalPeriods > 0;
                @endphp

                <span class="inline-flex rounded-full border px-3 py-1 text-xs font-semibold {{ $statusClasses }}">
                    {{ $statusLabel }}
                </span>
            @endif
        </div>

        @if ($latestAllocationRun)
            <div class="mt-6 grid gap-4 sm:grid-cols-3">
                <div class="rounded-md border border-slate-700 bg-slate-900 p-4">
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Cobertura
                    </p>

                    <p class="mt-2 text-2xl font-bold text-white">
                        {{ number_format((float) $latestAllocationRun->score, 2, ',', '.') }}%
                    </p>

                    <p class="mt-1 text-sm text-slate-400">
                        {{ $filledPeriods }} de {{ $totalPeriods }} períodos preenchidos
                    </p>
                </div>

                <div class="rounded-md border border-slate-700 bg-slate-900 p-4">
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Alocações
                    </p>

                    <p class="mt-2 text-2xl font-bold text-white">
                        {{ $latestAllocationRun->total_allocations }}
                    </p>
                </div>

                <div class="rounded-md border border-slate-700 bg-slate-900 p-4">
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Conflitos
                    </p>

                    <p class="mt-2 text-2xl font-bold text-white">
                        {{ $latestAllocationRun->total_conflicts }}
                    </p>
                </div>
            </div>

            <div class="mt-6">
                @if ($hasUsefulResult)
                    <a
                        href="{{ route('allocation-runs.show', $latestAllocationRun) }}"
                        class="inline-flex w-full items-center justify-center rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-500 sm:w-auto"
                    >
                        Ver resultado
                    </a>
                @else
                    <span class="text-sm text-slate-500">Sem resultado disponível.</span>
                @endif
            </div>
        @else
            <form
                action="{{ route('allocation-runs.store') }}"
                method="POST"
                class="mt-6"
                data-allocation-form
                data-confirmation="Deseja gerar uma nova alocação? Uma nova execução será registrada."
            >
                @csrf

                <button
                    type="submit"
                    data-submitting-text="Gerando..."
                    class="inline-flex w-full items-center justify-center rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 focus:ring-offset-slate-900 disabled:pointer-events-none sm:w-auto"
                >
                    Gerar primeira alocação
                </button>
            </form>
        @endif
    </section>

    <section class="mb-8">
        <h2 class="text-lg font-semibold mb-4">Indicadores principais</h2>

        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
            <div class="bg-slate-900 border border-slate-800 rounded p-5">
                <p class="text-sm text-slate-400">Professores ativos</p>
                <p class="mt-3 text-3xl font-bold">{{ $activeTeachersCount }}</p>
            </div>

            <div class="bg-slate-900 border border-slate-800 rounded p-5">
                <p class="text-sm text-slate-400">Turmas ativas</p>
                <p class="mt-3 text-3xl font-bold">{{ $activeSchoolClassesCount }}</p>
            </div>

            <div class="bg-slate-900 border border-slate-800 rounded p-5">
                <p class="text-sm text-slate-400">Unidades curriculares</p>
                <p class="mt-3 text-3xl font-bold">{{ $subjectsCount }}</p>
            </div>

            <div class="bg-slate-900 border border-slate-800 rounded p-5">
                <p class="text-sm text-slate-400">Unidades SENAI</p>
                <p class="mt-3 text-3xl font-bold">{{ $schoolUnitsCount }}</p>
            </div>
        </div>
    </section>

    <div class="grid gap-8 lg:grid-cols-2 mb-8">
        <section>
            <h2 class="text-lg font-semibold mb-4">Capacidade cadastrada</h2>

            <div class="grid gap-4 sm:grid-cols-3 lg:grid-cols-1">
                <div class="bg-slate-900 border border-slate-800 rounded p-5">
                    <p class="text-sm text-slate-400">Especialidades vinculadas</p>
                    <p class="mt-3 text-3xl font-bold">{{ $teacherSpecialtiesCount }}</p>
                </div>

                <div class="bg-slate-900 border border-slate-800 rounded p-5">
                    <p class="text-sm text-slate-400">Disponibilidades marcadas</p>
                    <p class="mt-3 text-3xl font-bold">{{ $availablePeriodsCount }}</p>
                </div>
            </div>
        </section>

        <section>
            <h2 class="text-lg font-semibold mb-4">Pendências de configuração</h2>

            <div class="bg-slate-900 border border-slate-800 rounded overflow-hidden">
                <div class="divide-y divide-slate-800">
                    <div class="flex items-center justify-between gap-4 px-5 py-4">
                        <span class="min-w-0 break-words text-slate-300">Professores sem especialidade</span>
                        <span class="font-bold text-yellow-400">{{ $teachersWithoutSpecialtyCount }}</span>
                    </div>

                    <div class="flex items-center justify-between gap-4 px-5 py-4">
                        <span class="min-w-0 break-words text-slate-300">Professores sem disponibilidade</span>
                        <span class="font-bold text-yellow-400">{{ $teachersWithoutAvailabilityCount }}</span>
                    </div>
                    
                    <div class="flex items-center justify-between gap-4 px-5 py-4">
                        <span class="min-w-0 break-words text-slate-300">Professores ativos sem especialidade</span>
                        <span class="font-bold text-yellow-400">{{ $activeTeachersWithoutSpecialtyCount }}</span>
                    </div>

                    <div class="flex items-center justify-between gap-4 px-5 py-4">
                        <span class="min-w-0 break-words text-slate-300">Professores inativos</span>
                        <span class="font-bold text-red-400">{{ $inactiveTeachersCount }}</span>
                    </div>
                    
                    <div class="flex items-center justify-between gap-4 px-5 py-4">
                        <span class="min-w-0 break-words text-slate-300">Turmas inativas</span>
                        <span class="font-bold text-red-400">{{ $inactiveClassesCount }}</span>
                    </div>

                    <div class="flex items-center justify-between gap-4 px-5 py-4">
                        <span class="min-w-0 break-words text-slate-300">
                            Turmas sem professor ativo compatível
                        </span>

                        <span
                            @class([
                                'font-bold',
                                'text-green-400' => $schoolClassesWithoutCompatibleTeacherCount === 0,
                                'text-red-400' => $schoolClassesWithoutCompatibleTeacherCount > 0,
                            ])
                        >
                            {{ $schoolClassesWithoutCompatibleTeacherCount }}
                        </span>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="grid gap-8 xl:grid-cols-2">
        <section>
            <div class="mb-4 flex flex-col items-start gap-2 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="text-lg font-semibold">Turmas recentes</h2>
                <a href="{{ route('school-classes.index') }}" class="text-sm text-blue-400 hover:text-blue-300">
                    Ver todas
                </a>
            </div>

            <div class="overflow-hidden rounded border border-slate-800 bg-slate-900">
                <div class="overflow-x-auto">
                <table class="min-w-[44rem] w-full">
                    <thead class="bg-slate-800">
                        <tr>
                            <th class="text-left px-4 py-3">Nome</th>
                            <th class="text-left px-4 py-3">Unidade curricular</th>
                            <th class="text-left px-4 py-3">Unidade SENAI</th>
                            <th class="text-left px-4 py-3">Períodos</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentSchoolClasses as $schoolClass)
                            <tr class="border-t border-slate-800">
                                <td class="px-4 py-3">
                                    <a href="{{ route('school-classes.show', $schoolClass) }}" class="text-blue-400 hover:text-blue-300">
                                        {{ $schoolClass->name }}
                                    </a>
                                </td>
                                <td class="px-4 py-3">
                                    {{ $schoolClass->subject?->name ?? 'Sem unidade curricular' }}
                                </td>
                                <td class="px-4 py-3">{{ $schoolClass->schoolUnit?->name ?? 'Sem unidade' }}</td>
                                <td class="px-4 py-3">{{ $schoolClass->required_periods }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-6 text-center text-slate-400">
                                    Nenhuma turma cadastrada.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                </div>
            </div>
        </section>

        <section>
            <div class="mb-4 flex flex-col items-start gap-2 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="text-lg font-semibold">Professores recentes</h2>
                <a href="{{ route('teachers.index') }}" class="text-sm text-blue-400 hover:text-blue-300">
                    Ver todos
                </a>
            </div>

            <div class="overflow-hidden rounded border border-slate-800 bg-slate-900">
                <div class="overflow-x-auto">
                <table class="min-w-[44rem] w-full">
                    <thead class="bg-slate-800">
                        <tr>
                            <th class="text-left px-4 py-3">Nome</th>
                            <th class="text-left px-4 py-3">Unidade</th>
                            <th class="text-left px-4 py-3">Especialidades</th>
                            <th class="text-left px-4 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentTeachers as $teacher)
                            <tr class="border-t border-slate-800">
                                <td class="px-4 py-3">
                                    <a href="{{ route('teachers.show', $teacher) }}" class="text-blue-400 hover:text-blue-300">
                                        {{ $teacher->name }}
                                    </a>
                                </td>
                                <td class="px-4 py-3">{{ $teacher->schoolUnit?->name ?? 'Sem unidade' }}</td>
                                <td class="px-4 py-3">
                                    @if ($teacher->specialties->isEmpty())
                                        <span class="text-yellow-400">Sem especialidade</span>
                                    @else
                                        {{ $teacher->specialties->pluck('name')->join(', ') }}
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if ($teacher->is_active)
                                        <span class="text-green-400">Ativo</span>
                                    @else
                                        <span class="text-red-400">Inativo</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-6 text-center text-slate-400">
                                    Nenhum professor cadastrado.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                </div>
            </div>
        </section>
    </div>
@endsection
