<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AllocationRun extends Model
{
    protected $fillable = [
        'status',
        'score',
        'total_allocations',
        'total_conflicts',
        'started_at',
        'finished_at',
    ];

    protected $casts = [
        'score' => 'float',
        'total_allocations' => 'integer',
        'total_conflicts' => 'integer',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function allocations(): HasMany
    {
        return $this->hasMany(Allocation::class);
    }

    public function conflicts(): HasMany
    {
        return $this->hasMany(AllocationConflict::class);
    }
}
