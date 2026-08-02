@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold">
                Gerações de alocação
            </h1>

            <p class="text-slate-400">
                Gere uma nova grade e consulte os resultados anteriores.
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
                class="inline-flex w-full items-center justify-center rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-500 disabled:pointer-events-none sm:w-auto"
            >
                Gerar nova alocação
            </button>
        </form>
    </div>

    @if (session('error'))
        <div class="mb-6 rounded border border-red-700 bg-red-900/40 px-4 py-3 text-red-200">
            {{ session('error') }}
        </div>
    @endif

    <div class="overflow-hidden rounded border border-slate-800 bg-slate-900">
        <div class="overflow-x-auto">
        <table class="min-w-[56rem] w-full">
            <thead class="bg-slate-800">
                <tr>
                    <th class="text-left px-4 py-3">Execução</th>
                    <th class="text-left px-4 py-3">Data</th>
                    <th class="text-left px-4 py-3">Status</th>
                    <th class="text-left px-4 py-3">Cobertura</th>
                    <th class="text-left px-4 py-3">Alocações</th>
                    <th class="text-left px-4 py-3">Conflitos</th>
                    <th class="text-right px-4 py-3">Ações</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($allocationRuns as $allocationRun)
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
                        $hasUsefulResult = $allocationRun->status !== 'failed' || $totalPeriods > 0;
                    @endphp

                    <tr class="border-t border-slate-800">
                        <td class="whitespace-nowrap px-4 py-3 font-semibold text-white">
                            #{{ $allocationRun->id }}
                        </td>

                        <td class="whitespace-nowrap px-4 py-3">
                            {{ $allocationRun->created_at?->format('d/m/Y H:i') }}
                        </td>

                        <td class="whitespace-nowrap px-4 py-3">
                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClasses }}">
                                {{ $statusLabel }}
                            </span>
                        </td>

                        <td class="whitespace-nowrap px-4 py-3">
                            <span class="block font-semibold text-white">
                                {{ number_format((float) $allocationRun->score, 2, ',', '.') }}%
                            </span>
                            <span class="mt-1 block text-sm text-slate-400">
                                {{ $filledPeriods }} de {{ $totalPeriods }} períodos
                            </span>
                        </td>

                        <td class="whitespace-nowrap px-4 py-3">
                            {{ $allocationRun->total_allocations }}
                        </td>

                        <td class="whitespace-nowrap px-4 py-3">
                            {{ $allocationRun->total_conflicts }}
                        </td>

                        <td class="whitespace-nowrap px-4 py-3 text-right">
                            @if ($hasUsefulResult)
                                <a href="{{ route('allocation-runs.show', $allocationRun) }}" class="text-blue-400">
                                    Visualizar
                                </a>
                            @else
                                <span class="text-slate-500">Sem resultado</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-6 text-center text-slate-400">
                            Nenhuma geração realizada.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $allocationRuns->links() }}
    </div>
@endsection
