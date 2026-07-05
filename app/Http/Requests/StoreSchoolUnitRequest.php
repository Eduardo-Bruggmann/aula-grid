<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSchoolUnitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:school_units,name'],
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
