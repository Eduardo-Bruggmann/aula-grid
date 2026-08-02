@extends('layouts.app')

@section('content')
<div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <div class="flex items-center gap-3">
            <a
                href="{{ route('allocation-runs.index') }}"
                class="text-sm font-medium text-blue-400 transition hover:text-blue-300"
            >
                Voltar
            </a>

            <span class="text-slate-600">/</span>

            <h2 class="text-xl font-semibold leading-tight text-white">
                Execução #{{ $allocationRun->id }}
            </h2>
        </div>

        <p class="mt-1 text-sm text-slate-400">
            Gerada em
            {{ $allocationRun->created_at?->format('d/m/Y \à\s H:i') }}
        </p>
    </div>

    <form
        action="{{ route('allocation-runs.store') }}"
        method="POST"
        class="w-full sm:w-auto"
        data-allocation-form
        data-confirmation="Deseja gerar uma nova alocação? Uma nova execução será registrada."
    >
        @csrf

        <button
            type="submit"
            data-submitting-text="Gerando..."
            class="inline-flex w-full items-center justify-center rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 focus:ring-offset-slate-900 disabled:pointer-events-none sm:w-auto"
        >
            Gerar novamente
        </button>
    </form>
</div>

@php
    $statusLabel = match ($allocationRun->status) {
        'completed' => 'Concluída',
        'completed_with_conflicts' => 'Concluída com conflitos',
        'running' => 'Em andamento',
        'failed' => 'Falhou',
        default => ucfirst(str_replace('_', ' ', $allocationRun->status)),
    };

    $statusClasses = match ($allocationRun->status) {
        'completed' => 'border border-emerald-500/30 bg-emerald-500/10 text-emerald-300',
        'completed_with_conflicts' => 'border border-amber-500/30 bg-amber-500/10 text-amber-300',
        'running' => 'border border-blue-500/30 bg-blue-500/10 text-blue-300',
        'failed' => 'border border-red-500/30 bg-red-500/10 text-red-300',
        default => 'border border-slate-600 bg-slate-800 text-slate-300',
    };

    $filledPeriods = $allocationRun->total_allocations;
    $totalPeriods = $filledPeriods + $allocationRun->total_conflicts;
@endphp

<div class="py-6 sm:py-8">
    <div class="mx-auto max-w-7xl space-y-8 px-0 sm:px-6 lg:px-8">
        @if (session('success'))
            <div class="rounded-md border border-green-700 bg-green-900/40 px-4 py-3 text-sm text-green-200">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-md border border-red-700 bg-red-900/40 px-4 py-3 text-sm text-red-200">
                {{ session('error') }}
            </div>
        @endif

        <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-md border border-slate-700 bg-slate-900 p-6">
                <p class="text-sm font-medium text-slate-400">
                    Status
                </p>

                <div class="mt-3">
                    <span class="inline-flex rounded-full px-3 py-1 text-sm font-semibold {{ $statusClasses }}">
                        {{ $statusLabel }}
                    </span>
                </div>
            </div>

            <div class="rounded-md border border-slate-700 bg-slate-900 p-6">
                <p class="text-sm font-medium text-slate-400">
                    Cobertura
                </p>

                <p class="mt-2 text-3xl font-bold text-white">
                    {{ number_format((float) $allocationRun->score, 2, ',', '.') }}%
                </p>

                <p class="mt-1 text-sm text-slate-400">
                    {{ $filledPeriods }} de {{ $totalPeriods }} períodos preenchidos
                </p>
            </div>

            <div class="rounded-md border border-slate-700 bg-slate-900 p-6">
                <p class="text-sm font-medium text-slate-400">
                    Alocações criadas
                </p>

                <p class="mt-2 text-3xl font-bold text-white">
                    {{ $allocationRun->total_allocations }}
                </p>
            </div>

            <div class="rounded-md border border-slate-700 bg-slate-900 p-6">
                <p class="text-sm font-medium text-slate-400">
                    Conflitos
                </p>

                <p class="mt-2 text-3xl font-bold text-white">
                    {{ $allocationRun->total_conflicts }}
                </p>
            </div>
        </section>

        <section class="overflow-hidden rounded bg-slate-900 border border-slate-800">
            <div class="border-b border-slate-800 px-6 py-5">
                <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-white">
                            Alocações geradas
                        </h3>

                        <p class="text-sm text-slate-400">
                            Professores selecionados para cada turma e período.
                        </p>
                    </div>

                    <span class="text-sm font-medium text-slate-400">
                        {{ $allocationRun->allocations->count() }}
                        registro(s)
                    </span>
                </div>
            </div>

            @if ($allocationRun->allocations->isEmpty())
                <div class="px-6 py-12 text-center">
                    <h4 class="text-base font-semibold text-white">
                        Nenhuma alocação criada
                    </h4>

                    <p class="mt-2 text-sm text-slate-400">
                        Esta execução não encontrou combinações válidas.
                    </p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-[64rem] w-full divide-y divide-slate-800">
                        <thead class="bg-slate-800">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-300">
                                    Turma
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-300">
                                    Unidade curricular
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-300">
                                    Unidade SENAI
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-300">
                                    Professor
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-300">
                                    Período
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-300">
                                    Pontuação
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-800 bg-slate-900">
                            @foreach ($allocationRun->allocations as $allocation)
                                <tr class="hover:bg-slate-800/60">
                                    <td class="whitespace-nowrap px-6 py-4 text-sm font-semibold text-white">
                                        {{ $allocation->schoolClass?->name ?? 'Turma não encontrada' }}
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-300">
                                        {{ $allocation->schoolClass?->subject?->name ?? 'Não informada' }}
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-300">
                                        {{ $allocation->schoolClass?->schoolUnit?->name ?? 'Não informada' }}
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-300">
                                        {{ $allocation->teacher?->name ?? 'Professor não encontrado' }}
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-300">
                                        <div class="font-medium text-white">
                                            {{ $allocation->period?->code ?? '—' }}
                                        </div>

                                        <div class="text-xs text-slate-400">
                                            {{ $allocation->period?->description }}
                                        </div>
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4 text-sm font-semibold text-white">
                                        {{ number_format((float) $allocation->score, 2, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

        <section class="overflow-hidden rounded bg-slate-900 border border-slate-800">
            <div class="border-b border-slate-800 px-6 py-5">
                <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-white">
                            Conflitos encontrados
                        </h3>

                        <p class="text-sm text-slate-400">
                            Períodos que não puderam ser preenchidos automaticamente.
                        </p>
                    </div>

                    <span class="text-sm font-medium text-slate-400">
                        {{ $allocationRun->conflicts->count() }}
                        registro(s)
                    </span>
                </div>
            </div>

            @if ($allocationRun->conflicts->isEmpty())
                <div class="px-6 py-12 text-center">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full border border-emerald-500/30 bg-emerald-500/10">
                        <svg
                            class="h-6 w-6 text-emerald-300"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                            aria-hidden="true"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M16.704 5.29a1 1 0 0 1 .006 1.414l-7.2 7.26a1 1 0 0 1-1.42 0l-3.8-3.83a1 1 0 1 1 1.42-1.408L8.8 11.84l6.49-6.544a1 1 0 0 1 1.414-.006Z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </div>

                    <h4 class="mt-4 text-base font-semibold text-white">
                        Nenhum conflito encontrado
                    </h4>

                    <p class="mt-2 text-sm text-slate-400">
                        Todas as necessidades desta execução foram atendidas.
                    </p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-[56rem] w-full divide-y divide-slate-800">
                        <thead class="bg-slate-800">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-300">
                                    Turma
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-300">
                                    Unidade curricular
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-300">
                                    Unidade SENAI
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-300">
                                    Período
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-300">
                                    Motivo
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-800 bg-slate-900">
                            @foreach ($allocationRun->conflicts as $conflict)
                                <tr class="hover:bg-slate-800/60">
                                    <td class="whitespace-nowrap px-6 py-4 text-sm font-semibold text-white">
                                        {{ $conflict->schoolClass?->name ?? 'Turma não encontrada' }}
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-300">
                                        {{ $conflict->schoolClass?->subject?->name ?? 'Não informada' }}
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-300">
                                        {{ $conflict->schoolClass?->schoolUnit?->name ?? 'Não informada' }}
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-300">
                                        <div class="font-medium text-white">
                                            {{ $conflict->period?->code ?? '—' }}
                                        </div>

                                        <div class="text-xs text-slate-400">
                                            {{ $conflict->period?->description }}
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 text-sm text-slate-300">
                                        {{ $conflict->reason_description ?? 'Nenhum professor válido foi encontrado.' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    </div>
</div>
@endsection
