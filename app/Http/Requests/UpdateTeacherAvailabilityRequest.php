<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTeacherAvailabilityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'periods' => ['nullable', 'array'],
            'periods.*' => ['integer', 'exists:periods,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'periods.array' => 'A disponibilidade enviada é inválida.',
            'periods.*.integer' => 'Um dos períodos enviados é inválido.',
            'periods.*.exists' => 'Um dos períodos selecionados não existe.',
        ];
    }
}
