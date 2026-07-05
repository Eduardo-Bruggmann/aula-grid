<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTeacherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'registration' => ['required', 'string', 'max:50', 'unique:teachers,registration'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', 'unique:teachers,email'],
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
