<div class="mb-4">
    <label for="specialty_id" class="block mb-2">Especialidade</label>

    <select
        id="specialty_id"
        name="specialty_id"
        class="w-full rounded bg-slate-900 border border-slate-700 px-3 py-2"
    >
        <option value="">Selecione uma especialidade</option>

        @foreach ($specialties as $specialty)
            <option
                value="{{ $specialty->id }}"
                @selected(old('specialty_id', $teacherSpecialty->specialty_id ?? '') == $specialty->id)
            >
                {{ $specialty->name }}
            </option>
        @endforeach
    </select>

    @error('specialty_id')
        <p class="text-red-400 mt-2">{{ $message }}</p>
    @enderror
</div>

<div class="mb-6">
    <label for="adherence_score" class="block mb-2">Aderência (%)</label>

    <input
        type="number"
        id="adherence_score"
        name="adherence_score"
        min="0"
        max="100"
        value="{{ old('adherence_score', $teacherSpecialty->adherence_score ?? 100) }}"
        class="w-full rounded bg-slate-900 border border-slate-700 px-3 py-2"
    >

    @error('adherence_score')
        <p class="text-red-400 mt-2">{{ $message }}</p>
    @enderror
</div>