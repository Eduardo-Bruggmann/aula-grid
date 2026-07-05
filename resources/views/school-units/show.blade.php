@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold">{{ $schoolUnit->name }}</h1>
        <p class="text-slate-400">Detalhes da unidade SENAI.</p>
    </div>

    <div class="bg-slate-900 border border-slate-800 rounded p-6 max-w-xl">
        <p><strong>ID:</strong> {{ $schoolUnit->id }}</p>
        <p><strong>Nome:</strong> {{ $schoolUnit->name }}</p>
        <p><strong>Criada em:</strong> {{ $schoolUnit->created_at->format('d/m/Y H:i') }}</p>
        <p><strong>Atualizada em:</strong> {{ $schoolUnit->updated_at->format('d/m/Y H:i') }}</p>
    </div>

    <div class="mt-6 flex gap-3">
        <a href="{{ route('school-units.edit', $schoolUnit) }}" class="bg-yellow-600 hover:bg-yellow-500 px-4 py-2 rounded">
            Editar
        </a>

        <a href="{{ route('school-units.index') }}" class="px-4 py-2 rounded bg-slate-800">
            Voltar
        </a>
    </div>
@endsection