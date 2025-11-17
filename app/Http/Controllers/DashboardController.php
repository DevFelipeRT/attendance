<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\ClassGroup\ClassGroup;
use App\Models\ClassGroup\ClassLesson;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

/**
 * HTTP controller for the main application dashboard.
 */
class DashboardController extends Controller
{
    /**
     * Display the dashboard with high-level overview metrics.
     */
    public function index(Request $request): View
    {
        $today = now()->toDateString();

        $studentsCount    = Student::query()->count();
        $teachersCount    = Teacher::query()->count();
        $subjectsCount    = Subject::query()->count();
        $classGroupsCount = ClassGroup::query()->count();

        $upcomingLessons = ClassLesson::query()
            ->with('classGroup')
            ->whereDate('lesson_date', '>=', $today)
            ->orderBy('lesson_date')
            ->orderBy('start_time')
            ->limit(10)
            ->get();

        $upcomingLessonsCount = $upcomingLessons->count();

        return view('dashboard.index', [
            'studentsCount'         => $studentsCount,
            'teachersCount'         => $teachersCount,
            'subjectsCount'         => $subjectsCount,
            'classGroupsCount'      => $classGroupsCount,
            'mentorshipsCount'      => null,
            'upcomingLessonsCount'  => $upcomingLessonsCount,
            'upcomingLessons'       => $upcomingLessons,
            'lowBalanceMentorships' => Collection::empty(),
        ]);
    }
}
