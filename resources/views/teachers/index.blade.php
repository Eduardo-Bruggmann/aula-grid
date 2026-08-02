@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold">Professores</h1>
            <p class="text-slate-400">Gerencie os professores cadastrados.</p>
        </div>

        <a href="{{ route('teachers.create') }}"
           class="inline-flex w-full items-center justify-center rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-500 sm:w-auto">
            Novo professor
        </a>
    </div>

    <div class="overflow-hidden rounded border border-slate-800 bg-slate-900">
        <div class="overflow-x-auto">
        <table class="min-w-[60rem] w-full">
            <thead class="bg-slate-800">
                <tr>
                    <th class="text-left px-4 py-3">Matrícula</th>
                    <th class="text-left px-4 py-3">Nome</th>
                    <th class="text-left px-4 py-3">Unidade</th>
                    <th class="text-left px-4 py-3">Carga</th>
                    <th class="text-left px-4 py-3">Status</th>
                    <th class="text-right px-4 py-3">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($teachers as $teacher)
                    @php
                        $hasAllocationHistory = $teacher->allocations_exists || $teacher->conflict_suggestions_exists;
                    @endphp

                    <tr class="border-t border-slate-800">
                        <td class="px-4 py-3">{{ $teacher->registration }}</td>
                        <td class="px-4 py-3">{{ $teacher->name }}</td>
                        <td class="px-4 py-3">{{ $teacher->schoolUnit->name }}</td>
                        <td class="px-4 py-3">
                            {{ $teacher->max_weekly_periods }} semanais /
                            {{ $teacher->max_daily_periods }} diários
                        </td>
                        <td class="px-4 py-3">
                            @if ($teacher->is_active)
                                <span class="text-green-400">Ativo</span>
                            @else
                                <span class="text-red-400">Inativo</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right space-x-2">
                            <a href="{{ route('teachers.show', $teacher) }}" class="text-blue-400">Ver</a>
                            <a href="{{ route('teachers.edit', $teacher) }}" class="text-yellow-400">Editar</a>

                            @if ($hasAllocationHistory)
                                @if ($teacher->is_active)
                                    <form action="{{ route('teachers.deactivate', $teacher) }}"
                                          method="POST"
                                          class="inline">
                                        @csrf
                                        @method('PATCH')

                                        <button
                                            type="submit"
                                            class="text-red-400"
                                            onclick="return confirm('Inativar este professor? Ele não será considerado em novas alocações.')"
                                        >
                                            Inativar
                                        </button>
                                    </form>
                                @endif
                            @else
                                <form action="{{ route('teachers.destroy', $teacher) }}"
                                      method="POST"
                                      class="inline">
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="text-red-400"
                                        onclick="return confirm('Remover este professor?')"
                                    >
                                        Remover
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-slate-400">
                            Nenhum professor cadastrado.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $teachers->links() }}
    </div>
@endsection
