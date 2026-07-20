<?php

namespace App\Domain\Allocation\Enums;

enum AllocationConflictCode: string
{
    case INACTIVE_TEACHER = 'INACTIVE_TEACHER';
    case INACTIVE_SCHOOL_CLASS = 'INACTIVE_SCHOOL_CLASS';
    case INCOMPATIBLE_SPECIALTY = 'INCOMPATIBLE_SPECIALTY';
    case TEACHER_UNAVAILABLE = 'TEACHER_UNAVAILABLE';
    case TEACHER_ALREADY_ALLOCATED = 'TEACHER_ALREADY_ALLOCATED';
    case SCHOOL_CLASS_ALREADY_ALLOCATED = 'SCHOOL_CLASS_ALREADY_ALLOCATED';
    case TEACHER_WEEKLY_LIMIT_EXCEEDED = 'TEACHER_WEEKLY_LIMIT_EXCEEDED';
    case TEACHER_DAILY_LIMIT_EXCEEDED = 'TEACHER_DAILY_LIMIT_EXCEEDED';
    case NO_VALID_CANDIDATE = 'NO_VALID_CANDIDATE';

    public function message(): string
    {
        return match ($this) {
            self::INACTIVE_TEACHER =>
            'O professor está inativo.',

            self::INACTIVE_SCHOOL_CLASS =>
            'A turma está inativa.',

            self::INCOMPATIBLE_SPECIALTY =>
            'O professor não possui especialidade compatível com a unidade curricular.',

            self::TEACHER_UNAVAILABLE =>
            'O professor não está disponível neste período.',

            self::TEACHER_ALREADY_ALLOCATED =>
            'O professor já está alocado em outra turma neste período.',

            self::SCHOOL_CLASS_ALREADY_ALLOCATED =>
            'A turma já possui um professor alocado neste período.',

            self::TEACHER_WEEKLY_LIMIT_EXCEEDED =>
            'O professor atingiu o limite semanal de períodos.',

            self::TEACHER_DAILY_LIMIT_EXCEEDED =>
            'O professor atingiu o limite diário de períodos.',

            self::NO_VALID_CANDIDATE =>
            'Nenhum professor válido foi encontrado para completar a alocação.',
        };
    }
}
