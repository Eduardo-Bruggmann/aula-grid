@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold">{{ $subject->name }}</h1>
        <p class="text-slate-400">Detalhes da UC.</p>
    </div>

    <div class="max-w-xl break-words rounded border border-slate-800 bg-slate-900 p-6">
        <p><strong>ID:</strong> {{ $subject->id }}</p>
        <p><strong>Nome:</strong> {{ $subject->name }}</p>
        <p><strong>Especialidade:</strong> {{ $subject->specialty->name}}</p>
        <p><strong>Criada em:</strong> {{ $subject->created_at->format('d/m/Y H:i') }}</p>
        <p><strong>Atualizada em:</strong> {{ $subject->updated_at->format('d/m/Y H:i') }}</p>
    </div>

    <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:flex-wrap">
        <a href="{{ route('subjects.edit', $subject) }}" class="bg-yellow-600 hover:bg-yellow-500 px-4 py-2 rounded">
            Editar
        </a>

        <a href="{{ route('subjects.index') }}" class="px-4 py-2 rounded bg-slate-800">
            Voltar
        </a>
    </div>
@endsection
