<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Allocation extends Model
{
    protected $fillable = [
        'allocation_run_id',
        'school_class_id',
        'teacher_id',
        'period_id',
        'status',
        'score',
    ];

    protected $casts = [
        'score' => 'integer',
    ];

    public function allocationRun(): BelongsTo
    {
        return $this->belongsTo(AllocationRun::class);
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(Period::class);
    }
}
