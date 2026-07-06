<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTeacherSpecialtyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $teacher = $this->route('teacher');
        $teacherSpecialty = $this->route('teacherSpecialty');

        return [
            'specialty_id' => [
                'required',
                'integer',
                'exists:specialties,id',
                Rule::unique('teacher_specialties', 'specialty_id')
                    ->where('teacher_id', $teacher->id)
                    ->ignore($teacherSpecialty->id),
            ],

            'adherence_score' => [
                'required',
                'integer',
                'min:0',
                'max:100',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'specialty_id.required' => 'A especialidade é obrigatória.',
            'specialty_id.exists' => 'A especialidade selecionada não existe.',
            'specialty_id.unique' => 'Este professor já possui essa especialidade.',

            'adherence_score.required' => 'A aderência é obrigatória.',
            'adherence_score.integer' => 'A aderência deve ser um número inteiro.',
            'adherence_score.min' => 'A aderência não pode ser menor que 0.',
            'adherence_score.max' => 'A aderência não pode ser maior que 100.',
        ];
    }
}
