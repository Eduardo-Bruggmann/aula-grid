<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SchoolClass extends Model
{
    protected $fillable = [
        'name',
        'subject_id',
        'school_unit_id',
        'required_periods',
        'is_active',
    ];

    protected $casts = [
        'required_periods' => 'integer',
        'is_active' => 'boolean',
    ];

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function schoolUnit(): BelongsTo
    {
        return $this->belongsTo(SchoolUnit::class);
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(Allocation::class);
    }

    public function conflicts(): HasMany
    {
        return $this->hasMany(AllocationConflict::class);
    }
}
