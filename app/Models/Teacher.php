<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Teacher extends Model
{
    protected $fillable = [
        'registration',
        'name',
        'email',
        'school_unit_id',
        'max_weekly_periods',
        'max_daily_periods',
        'is_active',
    ];

    protected $casts = [
        'max_weekly_periods' => 'integer',
        'max_daily_periods' => 'integer',
        'is_active' => 'boolean',
    ];

    public function schoolUnit(): BelongsTo
    {
        return $this->belongsTo(SchoolUnit::class);
    }

    public function specialties(): BelongsToMany
    {
        return $this->belongsToMany(Specialty::class, 'teacher_specialties')
            ->withPivot('adherence_score')
            ->withTimestamps();
    }

    public function teacherSpecialties(): HasMany
    {
        return $this->hasMany(TeacherSpecialty::class);
    }

    public function availabilities(): HasMany
    {
        return $this->hasMany(TeacherAvailability::class);
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(Allocation::class);
    }

    public function conflictSuggestions(): HasMany
    {
        return $this->hasMany(ConflictSuggestion::class);
    }
}
