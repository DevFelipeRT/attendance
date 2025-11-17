<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Student\StoreStudentRequest;
use App\Http\Requests\Student\UpdateStudentRequest;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * HTTP controller for managing students.
 */
class StudentController extends Controller
{
    /**
     * Display a listing of students.
     */
    public function index(): View
    {
        $students = Student::query()
            ->orderBy('name')
            ->get();

        return view('students.index', [
            'students' => $students,
        ]);
    }

    /**
     * Show the form for creating a new student.
     */
    public function create(): View
    {
        return view('students.create');
    }

    /**
     * Store a newly created student in storage.
     */
    public function store(StoreStudentRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $student = Student::query()->create($data);

        return redirect()
            ->route('students.edit', $student)
            ->with('status', 'Student successfully created.');
    }

    /**
     * Show the form for editing the specified student.
     */
    public function edit(Student $student): View
    {
        return view('students.edit', [
            'student' => $student,
        ]);
    }

    /**
     * Update the specified student in storage.
     */
    public function update(UpdateStudentRequest $request, Student $student): RedirectResponse
    {
        $data = $request->validated();

        $student->update($data);

        return redirect()
            ->route('students.edit', $student)
            ->with('status', 'Student successfully updated.');
    }

    /**
     * Remove the specified student from storage.
     */
    public function destroy(Student $student): RedirectResponse
    {
        $student->delete();

        return redirect()
            ->route('students.index')
            ->with('status', 'Student successfully deleted.');
    }
}
