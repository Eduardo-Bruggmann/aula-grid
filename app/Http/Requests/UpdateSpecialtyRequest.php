<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSpecialtyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $specialty = $this->route('specialty');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('specialties', 'name')->ignore($specialty),
            ],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome da unidade é obrigatório.',
            'name.unique' => 'Já existe uma unidade com esse nome.',
            'name.max' => 'O nome não pode ter mais de 255 caracteres.',
            'description.max' => 'A descrição não pode ter mais de 1000 caracteres.',
        ];
    }
}
