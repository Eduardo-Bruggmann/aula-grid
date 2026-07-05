<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SchoolUnitController;
use App\Http\Controllers\SpecialtyController;

Route::get('/', fn() => redirect()->route('school-units.index'));

Route::resource('school-units', SchoolUnitController::class);
Route::resource('specialties', SpecialtyController::class);
