<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSchoolClassRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'subject_id' => ['required', 'integer', Rule::exists('subjects', 'id')],
            'school_unit_id' => ['required', 'integer', Rule::exists('school_units', 'id')],
            'required_periods' => ['required', 'integer', 'min:1', 'max:15'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome da unidade curricular é obrigatório.',
            'name.max' => 'O nome não pode ter mais de 255 caracteres.',
            'subject_id.required' => 'A unidade curricular é obrigatória.',
            'subject_id.exists' => 'A unidade curricular selecionada não existe.',
            'school_unit_id.required' => 'A unidade escolar é obrigatória.',
            'school_unit_id.exists' => 'A unidade escolar selecionada não existe.',
            'required_periods.required' => 'O número de períodos é obrigatório.',
            'required_periods.min' => 'O número de períodos deve ser pelo menos 1.',
            'required_periods.max' => 'O número de períodos não pode ser maior que 15.',
        ];
    }
}
