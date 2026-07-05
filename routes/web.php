<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SchoolUnitController;

Route::get('/', fn() => redirect()->route('school-units.index'));

Route::resource('school-units', SchoolUnitController::class);
