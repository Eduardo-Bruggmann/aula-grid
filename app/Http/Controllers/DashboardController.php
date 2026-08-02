<?php

namespace App\Http\Controllers;

use App\Models\AllocationRun;
use App\Models\SchoolClass;
use App\Models\SchoolUnit;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TeacherAvailability;
use App\Models\TeacherSpecialty;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $activeTeachersCount = Teacher::query()
            ->where('is_active', true)
            ->count();

        $activeSchoolClassesCount = SchoolClass::query()
            ->where('is_active', true)
            ->count();

        $subjectsCount = Subject::query()->count();
        $schoolUnitsCount = SchoolUnit::query()->count();

        $teacherSpecialtiesCount = TeacherSpecialty::query()->count();

        $availablePeriodsCount = TeacherAvailability::query()
            ->where('is_available', true)
            ->count();

        $teachersWithoutSpecialtyCount = Teacher::query()
            ->whereDoesntHave('specialties')
            ->count();

        $activeTeachersWithoutSpecialtyCount = Teacher::query()
            ->where('is_active', true)
            ->whereDoesntHave('specialties')
            ->count();

        $teachersWithoutAvailabilityCount = Teacher::query()
            ->where('is_active', true)
            ->whereDoesntHave(
                'availabilities',
                fn (Builder $query) => $query->where('is_available', true)
            )
            ->count();

        $schoolClassesWithoutCompatibleTeacherCount = SchoolClass::query()
            ->where('is_active', true)
            ->whereDoesntHave(
                'subject.specialty.teachers',
                fn (Builder $query) => $query->where('teachers.is_active', true)
            )
            ->count();

        $inactiveClassesCount = SchoolClass::query()
            ->where('is_active', false)
            ->count();

        $inactiveTeachersCount = Teacher::query()
            ->where('is_active', false)
            ->count();

        $recentTeachers = Teacher::query()
            ->with([
                'schoolUnit:id,name',
                'specialties:id,name',
            ])
            ->latest()
            ->limit(5)
            ->get();

        $recentSchoolClasses = SchoolClass::query()
            ->with([
                'schoolUnit:id,name',
                'subject:id,name',
            ])
            ->latest()
            ->limit(5)
            ->get();

        $latestAllocationRun = AllocationRun::query()
            ->latest('created_at')
            ->first();

        return view('dashboard', compact(
            'activeTeachersCount',
            'activeSchoolClassesCount',
            'subjectsCount',
            'schoolUnitsCount',
            'teacherSpecialtiesCount',
            'availablePeriodsCount',
            'teachersWithoutSpecialtyCount',
            'activeTeachersWithoutSpecialtyCount',
            'teachersWithoutAvailabilityCount',
            'schoolClassesWithoutCompatibleTeacherCount',
            'inactiveClassesCount',
            'inactiveTeachersCount',
            'recentTeachers',
            'recentSchoolClasses',
            'latestAllocationRun',
        ));
    }
}
