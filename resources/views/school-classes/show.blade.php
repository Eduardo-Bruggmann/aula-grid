@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold">{{ $schoolClass->name }}</h1>
        <p class="text-slate-400">Detalhes da turma.</p>
    </div>

    <div class="max-w-2xl space-y-2 break-words rounded border border-slate-800 bg-slate-900 p-6">
        <p><strong>ID:</strong> {{ $schoolClass->id }}</p>
        <p><strong>Nome:</strong> {{ $schoolClass->name }}</p>
        <p><strong>Unidade Curricular:</strong> {{ $schoolClass->subject->name }}</p>
        <p><strong>Especialidade:</strong> {{ $schoolClass->subject->specialty->name }}</p>
        <p><strong>Unidade:</strong> {{ $schoolClass->schoolUnit->name }}</p>
        <p><strong>Períodos necessários:</strong> {{ $schoolClass->required_periods }}</p>
        <p>
            <strong>Status:</strong>
            {{ $schoolClass->is_active ? 'Ativa' : 'Inativa' }}
        </p>
        <p><strong>Criada em:</strong> {{ $schoolClass->created_at->format('d/m/Y H:i') }}</p>
        <p><strong>Atualizada em:</strong> {{ $schoolClass->updated_at->format('d/m/Y H:i') }}</p>
    </div>

    <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:flex-wrap">
        <a href="{{ route('school-classes.edit', $schoolClass) }}" class="bg-yellow-600 hover:bg-yellow-500 px-4 py-2 rounded">
            Editar
        </a>

        <a href="{{ route('school-classes.index') }}" class="px-4 py-2 rounded bg-slate-800">
            Voltar
        </a>
    </div>
@endsection
