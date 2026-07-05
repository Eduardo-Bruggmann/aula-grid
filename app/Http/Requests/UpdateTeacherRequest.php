<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTeacherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $teacher = $this->route('teacher');

        return [
            'registration' => [
                'required',
                'string',
                'max:50',
                Rule::unique('teachers', 'registration')->ignore($teacher),
            ],
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('teachers', 'email')->ignore($teacher),
            ],
            'school_unit_id' => ['required', 'integer', 'exists:school_units,id'],
            'max_weekly_periods' => ['required', 'integer', 'min:1', 'max:15'],
            'max_daily_periods' => ['required', 'integer', 'min:1', 'max:3'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'registration.required' => 'A matrícula é obrigatória.',
            'registration.max' => 'A matrícula é pode conter no máximo 50 caracteres.',
            'registration.unique' => 'A matrícula informada já existe.',
            'name.required' => 'O nome do professor é obrigatório.',
            'name.max' => 'O nome não pode ter mais de 255 caracteres.',
            'email.unique' => 'O email já está em uso.',
            'school_unit_id.required' => 'A unidade é obrigatória.',
            'school_unit_id.exists' => 'A unidade selecionada não existe.',
            'max_weekly_periods.required' => 'O número de períodos semanais é obrigatório.',
            'max_weekly_periods.min' => 'O número de períodos semanais deve ser no mínimo 1.',
            'max_weekly_periods.max' => 'O número de períodos semanais deve ser no máximo 15.',
            'max_daily_periods.required' => 'O número de períodos diários é obrigatório.',
            'max_daily_periods.min' => 'O número de períodos diários deve ser no mínimo 1.',
            'max_daily_periods.max' => 'O número de períodos diários deve ser no máximo 3.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
        ]);
    }
}
