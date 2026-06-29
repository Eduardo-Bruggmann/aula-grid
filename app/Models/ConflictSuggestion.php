<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConflictSuggestion extends Model
{
    protected $fillable = [
        'allocation_conflict_id',
        'teacher_id',
        'suggestion_score',
        'reason',
    ];

    protected $casts = [
        'suggestion_score' => 'integer',
    ];

    public function conflict(): BelongsTo
    {
        return $this->belongsTo(AllocationConflict::class, 'allocation_conflict_id');
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }
}
