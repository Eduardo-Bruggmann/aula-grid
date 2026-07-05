<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSubjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $subject = $this->route('subject');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('subjects', 'name')->ignore($subject),
            ],

            'specialty_id' => [
                'required',
                'integer',
                'exists:specialties,id',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome da unidade curricular é obrigatório.',
            'name.unique' => 'Já existe uma unidade curricular com esse nome.',
            'name.max' => 'O nome não pode ter mais de 255 caracteres.',
            'specialty_id.required' => 'A especialidade é obrigatória.',
            'specialty_id.exists' => 'A especialidade selecionada não existe.',
        ];
    }
}
