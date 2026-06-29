<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherSpecialty extends Model
{
    protected $fillable = [
        'teacher_id',
        'specialty_id',
        'adherence_score',
    ];

    protected $casts = [
        'adherence_score' => 'integer',
    ];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function specialty(): BelongsTo
    {
        return $this->belongsTo(Specialty::class);
    }
}
