@extends('layouts.app')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold">Unidades Curriculares</h1>
            <p class="text-slate-400">Gerencie as UCs cadastradas.</p>
        </div>

        <a href="{{ route('subjects.create') }}"
           class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded">
            Nova UC
        </a>
    </div>

    <div class="bg-slate-900 border border-slate-800 rounded overflow-hidden">
        <table class="w-full">
            <thead class="bg-slate-800">
                <tr>
                    <th class="text-left px-4 py-3">Nome</th>
                    <th class="text-left px-4 py-3">Especialidade</th>
                    <th class="text-right px-4 py-3">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($subjects as $subject)
                    <tr class="border-t border-slate-800">
                        <td class="px-4 py-3">{{ $subject->name }}</td>
                        <td class="px-4 py-3">{{ $subject->specialty->name }}</td>
                        <td class="px-4 py-3 text-right space-x-2">
                            <a href="{{ route('subjects.show', $subject) }}" class="text-blue-400">Ver</a>
                            <a href="{{ route('subjects.edit', $subject) }}" class="text-yellow-400">Editar</a>

                            <form action="{{ route('subjects.destroy', $subject) }}"
                                  method="POST"
                                  class="inline">
                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                        class="text-red-400"
                                        onclick="return confirm('Remover esta UC?')">
                                    Remover
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="px-4 py-6 text-center text-slate-400">
                            Nenhuma UC cadastrada.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $subjects->links() }}
    </div>
@endsection