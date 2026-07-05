<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSchoolUnitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $schoolUnit = $this->route('school_unit');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('school_units', 'name')->ignore($schoolUnit),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome da unidade é obrigatório.',
            'name.unique' => 'Já existe uma unidade com esse nome.',
            'name.max' => 'O nome não pode ter mais de 255 caracteres.',
        ];
    }
}
