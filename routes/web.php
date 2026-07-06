<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SchoolUnitController;
use App\Http\Controllers\SpecialtyController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\SchoolClassController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\TeacherSpecialtyController;

Route::get('/', fn() => redirect()->route('school-units.index'));

Route::resource('school-units', SchoolUnitController::class);
Route::resource('specialties', SpecialtyController::class);
Route::resource('subjects', SubjectController::class);
Route::resource('school-classes', SchoolClassController::class);

Route::resource('teachers', TeacherController::class);
Route::prefix('teachers/{teacher}')
    ->name('teachers.')
    ->group(function () {
        Route::resource('specialties', TeacherSpecialtyController::class)
            ->parameters([
                'specialties' => 'teacherSpecialty',
            ])
            ->except(['show']);
    });
