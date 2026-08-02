@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold">Unidades SENAI</h1>
            <p class="text-slate-400">Gerencie as unidades cadastradas.</p>
        </div>

        <a href="{{ route('school-units.create') }}"
           class="inline-flex w-full items-center justify-center rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-500 sm:w-auto">
            Nova unidade
        </a>
    </div>

    <div class="overflow-hidden rounded border border-slate-800 bg-slate-900">
        <div class="overflow-x-auto">
        <table class="min-w-[32rem] w-full">
            <thead class="bg-slate-800">
                <tr>
                    <th class="text-left px-4 py-3">Nome</th>
                    <th class="text-right px-4 py-3">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($schoolUnits as $schoolUnit)
                    <tr class="border-t border-slate-800">
                        <td class="px-4 py-3">{{ $schoolUnit->name }}</td>
                        <td class="px-4 py-3 text-right space-x-2">
                            <a href="{{ route('school-units.show', $schoolUnit) }}" class="text-blue-400">Ver</a>
                            <a href="{{ route('school-units.edit', $schoolUnit) }}" class="text-yellow-400">Editar</a>

                            <form action="{{ route('school-units.destroy', $schoolUnit) }}"
                                  method="POST"
                                  class="inline">
                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                        class="text-red-400"
                                        onclick="return confirm('Remover esta unidade?')">
                                    Remover
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="px-4 py-6 text-center text-slate-400">
                            Nenhuma unidade cadastrada.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $schoolUnits->links() }}
    </div>
@endsection
