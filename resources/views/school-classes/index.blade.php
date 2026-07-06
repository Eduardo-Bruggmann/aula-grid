@extends('layouts.app')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold">Turmas</h1>
            <p class="text-slate-400">Gerencie as turmas cadastradas.</p>
        </div>

        <a href="{{ route('school-classes.create') }}"
           class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded">
            Nova turma
        </a>
    </div>

    <div class="bg-slate-900 border border-slate-800 rounded overflow-hidden">
        <table class="w-full">
            <thead class="bg-slate-800">
                <tr>
                    <th class="text-left px-4 py-3">Nome</th>
                    <th class="text-left px-4 py-3">Unidade Curricular</th>
                    <th class="text-left px-4 py-3">Unidade</th>
                    <th class="text-left px-4 py-3">Períodos</th>
                    <th class="text-left px-4 py-3">Status</th>
                    <th class="text-right px-4 py-3">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($schoolClasses as $schoolClass)
                    <tr class="border-t border-slate-800">
                        <td class="px-4 py-3">{{ $schoolClass->name }}</td>
                        <td class="px-4 py-3">
                            {{ $schoolClass->subject->name }}

                            <span class="block text-sm text-slate-400">
                                {{ $schoolClass->subject->specialty->name }}
                            </span>
                        </td>
                        <td class="px-4 py-3">{{ $schoolClass->schoolUnit->name }}</td>
                        <td class="px-4 py-3">{{ $schoolClass->required_periods }}</td>
                        <td class="px-4 py-3">
                            @if ($schoolClass->is_active)
                                <span class="text-green-400">Ativo</span>
                            @else
                                <span class="text-red-400">Inativo</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right space-x-2">
                            <a href="{{ route('school-classes.show', $schoolClass) }}" class="text-blue-400">Ver</a>
                            <a href="{{ route('school-classes.edit', $schoolClass) }}" class="text-yellow-400">Editar</a>

                            <form action="{{ route('school-classes.destroy', $schoolClass) }}"
                                  method="POST"
                                  class="inline">
                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="text-red-400"
                                    onclick="return confirm('Remover esta turma?')"
                                >
                                    Remover
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-slate-400">
                            Nenhuma turma cadastrada.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $schoolClasses->links() }}
    </div>
@endsection
