@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold">{{ $specialty->name }}</h1>
        <p class="text-slate-400">Detalhes da unidade SENAI.</p>
    </div>

    <div class="bg-slate-900 border border-slate-800 rounded p-6 max-w-xl">
        <p><strong>ID:</strong> {{ $specialty->id }}</p>
        <p><strong>Nome:</strong> {{ $specialty->name }}</p>
        <p><strong>Descrição:</strong> {{ $specialty->description }}</p>
        <p><strong>Criada em:</strong> {{ $specialty->created_at->format('d/m/Y H:i') }}</p>
        <p><strong>Atualizada em:</strong> {{ $specialty->updated_at->format('d/m/Y H:i') }}</p>
    </div>

    <div class="mt-6 flex gap-3">
        <a href="{{ route('specialties.edit', $specialty) }}" class="bg-yellow-600 hover:bg-yellow-500 px-4 py-2 rounded">
            Editar
        </a>

        <a href="{{ route('specialties.index') }}" class="px-4 py-2 rounded bg-slate-800">
            Voltar
        </a>
    </div>
@endsection