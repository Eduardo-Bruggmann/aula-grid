<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Specialty extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(Teacher::class, 'teacher_specialties')
            ->withPivot('adherence_score')
            ->withTimestamps();
    }

    public function teacherSpecialties(): HasMany
    {
        return $this->hasMany(TeacherSpecialty::class);
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class);
    }
}
