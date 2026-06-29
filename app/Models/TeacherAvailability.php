<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherAvailability extends Model
{
    protected $fillable = [
        'teacher_id',
        'period_id',
        'is_available',
    ];

    protected $casts = [
        'is_available' => 'boolean',
    ];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(Period::class);
    }
}
