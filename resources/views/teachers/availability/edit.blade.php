@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold">
            Disponibilidade de {{ $teacher->name }}
        </h1>

        <p class="text-slate-400">
            Matrícula: {{ $teacher->registration }}
        </p>
    </div>

    <form
        action="{{ route('teachers.availability.update', $teacher) }}"
        method="POST"
    >
        @csrf
        @method('PUT')

        <div class="overflow-hidden rounded border border-slate-800 bg-slate-900">
            <div class="overflow-x-auto">
            <table class="min-w-[44rem] w-full">
                <thead class="bg-slate-800">
                    <tr>
                        <th class="text-left px-4 py-3">Código</th>
                        <th class="text-left px-4 py-3">Dia</th>
                        <th class="text-left px-4 py-3">Turno</th>
                        <th class="text-left px-4 py-3">Descrição</th>
                        <th class="text-center px-4 py-3">Disponível</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($periods as $period)
                        <tr class="border-t border-slate-800">
                            <td class="px-4 py-3">
                                {{ $period->code }}
                            </td>

                            <td class="px-4 py-3">
                                {{ $period->weekday_label }}
                            </td>

                            <td class="px-4 py-3">
                                {{ $period->shift_label }}
                            </td>

                            <td class="px-4 py-3">
                                {{ $period->description }}
                            </td>

                            <td class="px-4 py-3 text-center">
                                <input
                                    type="checkbox"
                                    name="periods[]"
                                    value="{{ $period->id }}"
                                    @checked(in_array($period->id, old('periods', $availablePeriodIds), true))
                                    class="h-4 w-4"
                                >
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
        </div>

        @error('periods')
            <p class="text-red-400 mt-3">{{ $message }}</p>
        @enderror

        @error('periods.*')
            <p class="text-red-400 mt-3">{{ $message }}</p>
        @enderror

        <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:flex-wrap">
            <button
                type="submit"
                class="bg-blue-600 hover:bg-blue-500 px-4 py-2 rounded"
            >
                Salvar disponibilidade
            </button>

            <a
                href="{{ route('teachers.show', $teacher) }}"
                class="px-4 py-2 rounded bg-slate-800"
            >
                Voltar ao professor
            </a>
        </div>
    </form>
@endsection
