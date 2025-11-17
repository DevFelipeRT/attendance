<?php

declare(strict_types=1);

namespace App\Http\Controllers\ClassGroup;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClassGroup\StoreClassGroupRequest;
use App\Http\Requests\ClassGroup\UpdateClassGroupRequest;
use App\Models\ClassGroup\ClassGroup;
use App\Models\Subject;
use App\Models\Teacher;
use App\Services\ClassGroup\ClassGroupService;
use DomainException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * HTTP controller for managing class groups.
 */
class ClassGroupController extends Controller
{
    public function __construct(
        private readonly ClassGroupService $classGroupService,
    ) {
    }

    /**
     * Display a listing of class groups.
     */
    public function index(): View
    {
        $classGroups = ClassGroup::query()
            ->with(['subject', 'teacher'])
            ->orderBy('name')
            ->get();

        return view('class-groups.index', [
            'classGroups' => $classGroups,
        ]);
    }

    /**
     * Show the form for creating a new class group.
     */
    public function create(): View
    {
        $subjects = Subject::query()
            ->orderBy('name')
            ->get();

        $teachers = Teacher::query()
            ->orderBy('name')
            ->get();

        return view('class-groups.create', [
            'subjects' => $subjects,
            'teachers' => $teachers,
        ]);
    }

    /**
     * Store a newly created class group in storage.
     */
    public function store(StoreClassGroupRequest $request): RedirectResponse
    {
        $data = $request->validated();

        try {
            $classGroup = $this->classGroupService->createClassGroup($data);
        } catch (DomainException|ModelNotFoundException $exception) {
            return back()
                ->withInput()
                ->withErrors([
                    'class_group' => $exception->getMessage(),
                ]);
        }

        return redirect()
            ->route('class-groups.show', $classGroup)
            ->with('status', 'Class group successfully created.');
    }

    /**
     * Display the specified class group.
     */
    public function show(ClassGroup $classGroup): View
    {
        $classGroup->load([
            'subject',
            'teacher',
            'enrollments.student',
            'lessons',
        ]);

        return view('class-groups.show', [
            'classGroup'  => $classGroup,
            'enrollments' => $classGroup->enrollments,
            'lessons'     => $classGroup->lessons,
        ]);
    }

    /**
     * Show the form for editing the specified class group.
     */
    public function edit(ClassGroup $classGroup): View
    {
        $subjects = Subject::query()
            ->orderBy('name')
            ->get();

        $teachers = Teacher::query()
            ->orderBy('name')
            ->get();

        return view('class-groups.edit', [
            'classGroup' => $classGroup,
            'subjects'   => $subjects,
            'teachers'   => $teachers,
        ]);
    }

    /**
     * Update the specified class group in storage.
     */
    public function update(UpdateClassGroupRequest $request, ClassGroup $classGroup): RedirectResponse
    {
        $data = $request->validated();

        try {
            $this->classGroupService->updateClassGroup($classGroup, $data);
        } catch (DomainException|ModelNotFoundException $exception) {
            return back()
                ->withInput()
                ->withErrors([
                    'class_group' => $exception->getMessage(),
                ]);
        }

        return redirect()
            ->route('class-groups.show', $classGroup)
            ->with('status', 'Class group successfully updated.');
    }

    /**
     * Remove the specified class group from storage.
     */
    public function destroy(ClassGroup $classGroup): RedirectResponse
    {
        try {
            $classGroup->delete();
        } catch (DomainException|ModelNotFoundException $exception) {
            return back()
                ->withErrors([
                    'class_group' => $exception->getMessage(),
                ]);
        }

        return redirect()
            ->route('class-groups.index')
            ->with('status', 'Class group successfully deleted.');
    }
}
