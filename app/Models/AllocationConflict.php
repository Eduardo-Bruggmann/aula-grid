<?php

namespace App\Models;

use App\Domain\Allocation\Enums\AllocationConflictCode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AllocationConflict extends Model
{
    protected $fillable = [
        'allocation_run_id',
        'school_class_id',
        'period_id',
        'reason_code',
        'reason_description',
        'status',
    ];

    protected $casts = [
        'reason_code' => AllocationConflictCode::class,
    ];

    public function allocationRun(): BelongsTo
    {
        return $this->belongsTo(AllocationRun::class);
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(Period::class);
    }

    public function suggestions(): HasMany
    {
        return $this->hasMany(ConflictSuggestion::class);
    }
}
