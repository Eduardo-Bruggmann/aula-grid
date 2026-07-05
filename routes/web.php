<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SchoolUnitController;
use App\Http\Controllers\SpecialtyController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TeacherController;

Route::get('/', fn() => redirect()->route('school-units.index'));

Route::resource('school-units', SchoolUnitController::class);
Route::resource('specialties', SpecialtyController::class);
Route::resource('subjects', SubjectController::class);
Route::resource('teachers', TeacherController::class);
