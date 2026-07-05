@extends('layouts.app')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold">Especialidades</h1>
            <p class="text-slate-400">Gerencie as especialidades cadastradas.</p>
        </div>

        <a href="{{ route('specialties.create') }}"
           class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded">
            Nova especialidade
        </a>
    </div>

    <div class="bg-slate-900 border border-slate-800 rounded overflow-hidden">
        <table class="w-full">
            <thead class="bg-slate-800">
                <tr>
                    <th class="text-left px-4 py-3">Nome</th>
                    <th class="text-left px-4 py-3">Descrição</th>
                    <th class="text-right px-4 py-3">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($specialties as $specialty)
                    <tr class="border-t border-slate-800">
                        <td class="px-4 py-3">{{ $specialty->name }}</td>
                        <td class="px-4 py-3">{{ $specialty->description }}</td>
                        <td class="px-4 py-3 text-right space-x-2">
                            <a href="{{ route('specialties.show', $specialty) }}" class="text-blue-400">Ver</a>
                            <a href="{{ route('specialties.edit', $specialty) }}" class="text-yellow-400">Editar</a>

                            <form action="{{ route('specialties.destroy', $specialty) }}"
                                  method="POST"
                                  class="inline">
                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                        class="text-red-400"
                                        onclick="return confirm('Remover esta especialidade?')">
                                    Remover
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="px-4 py-6 text-center text-slate-400">
                            Nenhuma especialidade cadastrada.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $specialties->links() }}
    </div>
@endsection