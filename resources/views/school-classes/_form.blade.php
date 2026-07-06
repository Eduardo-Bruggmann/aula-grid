<div class="mb-4">
    <label for="name" class="block mb-2">Nome</label>
    <input
        type="text"
        id="name"
        name="name"
        value="{{ old('name', $schoolClass->name ?? '') }}"
        class="w-full rounded bg-slate-900 border border-slate-700 px-3 py-2"
    >
    @error('name')
        <p class="text-red-400 mt-2">{{ $message }}</p>
    @enderror
</div>

<div class="mb-4">
    <label for="subject_id" class="block mb-2">Unidade Curricular</label>
    <select
        id="subject_id"
        name="subject_id"
        class="w-full rounded bg-slate-900 border border-slate-700 px-3 py-2 text-slate-100 shadow-sm"
    >
        <option value="" disabled @selected(old('subject_id', $schoolClass->subject_id ?? '') === '')>
            Selecione uma unidade curricular
        </option>

        @foreach ($subjects as $subject)
            <option
                value="{{ $subject->id }}"
                class="bg-slate-900 text-slate-100"
                @selected(old('subject_id', $schoolClass->subject_id ?? '') == $subject->id)
            >
                {{ $subject->name }}
            </option>
        @endforeach
    </select>
    @error('subject_id')
        <p class="text-red-400 mt-2">{{ $message }}</p>
    @enderror
</div>

<div class="mb-4">
    <label for="school_unit_id" class="block mb-2">Unidade SENAI</label>
    <select
        id="school_unit_id"
        name="school_unit_id"
        class="w-full rounded bg-slate-900 border border-slate-700 px-3 py-2 text-slate-100 shadow-sm"
    >
        <option value="" disabled @selected(old('school_unit_id', $schoolClass->school_unit_id ?? '') === '')>
            Selecione uma unidade
        </option>

        @foreach ($schoolUnits as $schoolUnit)
            <option
                value="{{ $schoolUnit->id }}"
                class="bg-slate-900 text-slate-100"
                @selected(old('school_unit_id', $schoolClass->school_unit_id ?? '') == $schoolUnit->id)
            >
                {{ $schoolUnit->name }}
            </option>
        @endforeach
    </select>
    @error('school_unit_id')
        <p class="text-red-400 mt-2">{{ $message }}</p>
    @enderror
</div>

<div class="mb-4">
    <label for="required_periods" class="block mb-2">Períodos necessários</label>
    <input
        type="number"
        id="required_periods"
        name="required_periods"
        min="1"
        max="15"
        value="{{ old('required_periods', $schoolClass->required_periods ?? 7) }}"
        class="w-full rounded bg-slate-900 border border-slate-700 px-3 py-2"
    >
    @error('required_periods')
        <p class="text-red-400 mt-2">{{ $message }}</p>
    @enderror
</div>

<div class="mb-6">
    <input type="hidden" name="is_active" value="0">

    <label class="inline-flex items-center gap-2">
        <input
            type="checkbox"
            name="is_active"
            value="1"
            @checked(old('is_active', $schoolClass->is_active ?? true))
        >
        <span>Turma ativa</span>
    </label>
    @error('is_active')
        <p class="text-red-400 mt-2">{{ $message }}</p>
    @enderror
</div>
