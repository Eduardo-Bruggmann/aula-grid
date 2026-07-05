<div class="mb-4">
    <label for="registration" class="block mb-2">Matrícula</label>
    <input
        type="text"
        id="registration"
        name="registration"
        value="{{ old('registration', $teacher->registration ?? '') }}"
        class="w-full rounded bg-slate-900 border border-slate-700 px-3 py-2"
    >
    @error('registration')
        <p class="text-red-400 mt-2">{{ $message }}</p>
    @enderror
</div>

<div class="mb-4">
    <label for="name" class="block mb-2">Nome</label>
    <input
        type="text"
        id="name"
        name="name"
        value="{{ old('name', $teacher->name ?? '') }}"
        class="w-full rounded bg-slate-900 border border-slate-700 px-3 py-2"
    >
    @error('name')
        <p class="text-red-400 mt-2">{{ $message }}</p>
    @enderror
</div>

<div class="mb-4">
    <label for="email" class="block mb-2">E-mail</label>
    <input
        type="email"
        id="email"
        name="email"
        value="{{ old('email', $teacher->email ?? '') }}"
        class="w-full rounded bg-slate-900 border border-slate-700 px-3 py-2"
    >
    @error('email')
        <p class="text-red-400 mt-2">{{ $message }}</p>
    @enderror
</div>

<div class="mb-4">
    <label for="school_unit_id" class="block mb-2">Unidade SENAI</label>
    <select
        id="school_unit_id"
        name="school_unit_id"
        class="w-full rounded bg-slate-900 border border-slate-700 px-3 py-2"
    >
        <option value="">Selecione uma unidade</option>

        @foreach ($schoolUnits as $schoolUnit)
            <option
                value="{{ $schoolUnit->id }}"
                @selected(old('school_unit_id', $teacher->school_unit_id ?? '') == $schoolUnit->id)
            >
                {{ $schoolUnit->name }}
            </option>
        @endforeach
    </select>
    @error('school_unit_id')
        <p class="text-red-400 mt-2">{{ $message }}</p>
    @enderror
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="mb-4">
        <label for="max_weekly_periods" class="block mb-2">Máximo semanal de períodos</label>
        <input
            type="number"
            id="max_weekly_periods"
            name="max_weekly_periods"
            min="1"
            max="15"
            value="{{ old('max_weekly_periods', $teacher->max_weekly_periods ?? 7) }}"
            class="w-full rounded bg-slate-900 border border-slate-700 px-3 py-2"
        >
        @error('max_weekly_periods')
            <p class="text-red-400 mt-2">{{ $message }}</p>
        @enderror
    </div>

    <div class="mb-4">
        <label for="max_daily_periods" class="block mb-2">Máximo diário de períodos</label>
        <input
            type="number"
            id="max_daily_periods"
            name="max_daily_periods"
            min="1"
            max="3"
            value="{{ old('max_daily_periods', $teacher->max_daily_periods ?? 2) }}"
            class="w-full rounded bg-slate-900 border border-slate-700 px-3 py-2"
        >
        @error('max_daily_periods')
            <p class="text-red-400 mt-2">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="mb-6">
    <label class="inline-flex items-center gap-2">
        <input
            type="checkbox"
            name="is_active"
            value="1"
            @checked(old('is_active', $teacher->is_active ?? true))
        >
        <span>Professor ativo</span>
    </label>
    @error('is_active')
        <p class="text-red-400 mt-2">{{ $message }}</p>
    @enderror
</div>