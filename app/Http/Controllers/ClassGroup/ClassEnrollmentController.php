<?php

declare(strict_types=1);

namespace App\Http\Controllers\ClassGroup;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClassGroup\StoreClassEnrollmentRequest;
use App\Models\ClassGroup\ClassEnrollment;
use App\Models\ClassGroup\ClassGroup;
use App\Models\Student;
use App\Services\ClassGroup\ClassEnrollmentService;
use DomainException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * HTTP controller for managing class enrollments within a class group.
 */
class ClassEnrollmentController extends Controller
{
    public function __construct(
        private readonly ClassEnrollmentService $classEnrollmentService,
    ) {
    }

    /**
     * Display a listing of enrollments for the given class group.
     */
    public function index(ClassGroup $classGroup): View
    {
        $classGroup->load([
            'enrollments.student',
        ]);

        $students = Student::query()
            ->orderBy('name')
            ->get();

        return view('class-groups.enrollments.index', [
            'classGroup'  => $classGroup,
            'enrollments' => $classGroup->enrollments,
            'students'    => $students,
        ]);
    }

    /**
     * Store a newly created enrollment for the given class group.
     */
    public function store(StoreClassEnrollmentRequest $request, ClassGroup $classGroup): RedirectResponse
    {
        $data = $request->validated();

        try {
            $student = Student::query()->findOrFail($data['student_id']);

            $this->classEnrollmentService->enrollStudent(
                $classGroup,
                $student,
                $data['enrolled_at'] ?? null,
            );
        } catch (DomainException|ModelNotFoundException $exception) {
            return back()
                ->withInput()
                ->withErrors([
                    'enrollment' => $exception->getMessage(),
                ]);
        }

        return redirect()
            ->route('class-groups.enrollments.index', $classGroup)
            ->with('status', 'Student successfully enrolled.');
    }

    /**
     * Remove the specified enrollment from the given class group.
     */
    public function destroy(ClassGroup $classGroup, ClassEnrollment $enrollment): RedirectResponse
    {
        if ((int) $enrollment->class_group_id !== (int) $classGroup->id) {
            abort(404);
        }

        try {
            $this->classEnrollmentService->unenrollStudent($enrollment);
        } catch (DomainException|ModelNotFoundException $exception) {
            return back()
                ->withErrors([
                    'enrollment' => $exception->getMessage(),
                ]);
        }

        return redirect()
            ->route('class-groups.enrollments.index', $classGroup)
            ->with('status', 'Enrollment successfully removed.');
    }
}
