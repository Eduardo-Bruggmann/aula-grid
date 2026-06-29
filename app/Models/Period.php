<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Period extends Model
{
    protected $fillable = [
        'code',
        'weekday',
        'shift',
        'description',
        'sort_order',
    ];

    protected $casts = [
        'weekday' => 'integer',
        'sort_order' => 'integer',
    ];

    public function availabilities(): HasMany
    {
        return $this->hasMany(TeacherAvailability::class);
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
