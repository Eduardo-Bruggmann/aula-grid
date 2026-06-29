<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SchoolUnit extends Model
{
    protected $fillable = [
        'name',
    ];

    public function teachers(): HasMany
    {
        return $this->hasMany(Teacher::class);
    }

    public function schoolClasses(): HasMany
    {
        return $this->hasMany(SchoolClass::class);
    }
}
