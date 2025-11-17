<?php

declare(strict_types=1);

namespace App\Http\Controllers\ClassGroup;

use App\Http\Controllers\Controller;
use App\Models\ClassGroup\ClassEnrollment;
use App\Models\ClassGroup\ClassGroup;
use App\Models\ClassGroup\ClassLesson;
use App\Services\ClassGroup\AttendanceService;
use DomainException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * HTTP controller for managing attendance for a given lesson.
 */
class AttendanceController extends Controller
{
    public function __construct(
        private readonly AttendanceService $attendanceService,
    ) {
    }

    /**
     * Show the attendance form for a lesson.
     */
    public function edit(ClassGroup $classGroup, ClassLesson $lesson): View
    {
        if ((int) $lesson->class_group_id !== (int) $classGroup->id) {
            abort(404);
        }

        $lesson->load([
            'classGroup.subject',
            'classGroup.teacher',
            'classGroup.enrollments.student',
            'attendances',
        ]);

        $classGroup = $lesson->classGroup;

        $rows = $classGroup->enrollments->map(
            static function (ClassEnrollment $enrollment) use ($lesson): array {
                $attendance = $lesson->attendances
                    ->firstWhere('student_id', $enrollment->student_id);

                return [
                    'enrollment' => $enrollment,
                    'student'    => $enrollment->student,
                    'attendance' => $attendance,
                ];
            }
        );

        return view('class-groups.attendance.edit', [
            'lesson'     => $lesson,
            'classGroup' => $classGroup,
            'rows'       => $rows,
        ]);
    }

    /**
     * Persist attendance for a lesson.
     *
     * Expects payload in the shape:
     * rows[<enrollment_id>][status]
     * rows[<enrollment_id>][absence_notified]
     */
    public function update(Request $request, ClassGroup $classGroup, ClassLesson $lesson): RedirectResponse
    {
        if ((int) $lesson->class_group_id !== (int) $classGroup->id) {
            abort(404);
        }

        $lesson->load(['classGroup.enrollments.student']);

        $validated = $request->validate([
            'rows'                    => ['required', 'array'],
            'rows.*.status'           => ['required', 'string'],
            'rows.*.absence_notified' => ['nullable', 'boolean'],
        ]);

        $items = [];

        foreach ($lesson->classGroup->enrollments as $enrollment) {
            $key = (string) $enrollment->getKey();

            if (! array_key_exists($key, $validated['rows'])) {
                continue;
            }

            $row = $validated['rows'][$key];

            $items[] = [
                'student_id'       => $enrollment->student_id,
                'status'           => $row['status'],
                'absence_notified' => (bool) ($row['absence_notified'] ?? false),
            ];
        }

        if ($items === []) {
            return back()
                ->withInput()
                ->withErrors([
                    'attendance' => 'No attendance data was provided.',
                ]);
        }

        try {
            $this->attendanceService->registerLessonAttendance($lesson, $items);
        } catch (DomainException|ModelNotFoundException $exception) {
            return back()
                ->withInput()
                ->withErrors([
                    'attendance' => $exception->getMessage(),
                ]);
        }

        return redirect()
            ->route('class-groups.lessons.attendance.edit', [$classGroup, $lesson])
            ->with('status', 'Attendance successfully saved.');
    }
}
