<?php

declare(strict_types=1);

namespace App\Http\Controllers\ClassGroup;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClassGroup\StoreClassLessonRequest;
use App\Http\Requests\ClassGroup\UpdateClassLessonRequest;
use App\Models\ClassGroup\ClassGroup;
use App\Models\ClassGroup\ClassLesson;
use App\Services\ClassGroup\ClassLessonService;
use DomainException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * HTTP controller for managing class lessons for regular class groups.
 */
class ClassLessonController extends Controller
{
    public function __construct(
        private readonly ClassLessonService $classLessonService,
    ) {
    }

    /**
     * Display a listing of lessons for the given class group.
     */
    public function index(ClassGroup $classGroup): View
    {
        $classGroup->load('lessons');

        $lessons = $classGroup->lessons()
            ->orderBy('lesson_date')
            ->orderBy('start_time')
            ->get();

        return view('class-groups.show', [
            'classGroup' => $classGroup,
            'lessons'    => $lessons,
        ]);
    }

    /**
     * Show the form for creating a new lesson for the given class group.
     */
    public function create(ClassGroup $classGroup): View
    {
        return view('class-groups.lessons.create', [
            'classGroup' => $classGroup,
        ]);
    }

    /**
     * Store a newly created lesson for the given class group.
     */
    public function store(StoreClassLessonRequest $request, ClassGroup $classGroup): RedirectResponse
    {
        $data = $request->validated();

        $attributes = array_merge($data, [
            'class_group_id' => $classGroup->id,
        ]);

        try {
            $this->classLessonService->createLesson($attributes);
        } catch (DomainException|ModelNotFoundException $exception) {
            return back()
                ->withInput()
                ->withErrors([
                    'lesson' => $exception->getMessage(),
                ]);
        }

        return redirect()
            ->route('class-groups.show', $classGroup)
            ->with('status', 'Lesson successfully created.');
    }

    /**
     * Show the form for editing the specified lesson.
     */
    public function edit(ClassGroup $classGroup, ClassLesson $lesson): View
    {
        if ((int) $lesson->class_group_id !== (int) $classGroup->id) {
            abort(404);
        }

        return view('class-groups.lessons.edit', [
            'classGroup' => $classGroup,
            'lesson'     => $lesson,
        ]);
    }

    /**
     * Update the specified lesson.
     */
    public function update(
        UpdateClassLessonRequest $request,
        ClassGroup $classGroup,
        ClassLesson $lesson
    ): RedirectResponse {
        if ((int) $lesson->class_group_id !== (int) $classGroup->id) {
            abort(404);
        }

        $data = $request->validated();

        try {
            $this->classLessonService->updateLesson($lesson, $data);
        } catch (DomainException|ModelNotFoundException $exception) {
            return back()
                ->withInput()
                ->withErrors([
                    'lesson' => $exception->getMessage(),
                ]);
        }

        return redirect()
            ->route('class-groups.show', $classGroup)
            ->with('status', 'Lesson successfully updated.');
    }

    /**
     * Cancel the specified lesson for the given class group.
     */
    public function cancel(ClassGroup $classGroup, ClassLesson $lesson): RedirectResponse
    {
        if ((int) $lesson->class_group_id !== (int) $classGroup->id) {
            abort(404);
        }

        try {
            $this->classLessonService->cancelLesson($lesson);
        } catch (DomainException|ModelNotFoundException $exception) {
            return back()
                ->withErrors([
                    'lesson' => $exception->getMessage(),
                ]);
        }

        return redirect()
            ->route('class-groups.show', $classGroup)
            ->with('status', 'Lesson successfully cancelled.');
    }

    /**
     * Delete the specified lesson for the given class group.
     */
    public function destroy(ClassGroup $classGroup, ClassLesson $lesson): RedirectResponse
    {
        if ((int) $lesson->class_group_id !== (int) $classGroup->id) {
            abort(404);
        }

        $this->classLessonService->destroyLesson($lesson);

        return redirect()
            ->route('class-groups.show', $classGroup)
            ->with('status', 'Lesson successfully deleted.');
    }

    /**
     * Generate lessons for a regular class group within an optional date range.
     */
    public function generate(Request $request, ClassGroup $classGroup): RedirectResponse
    {
        $data = $request->validate([
            'from' => ['nullable', 'date'],
            'to'   => ['nullable', 'date'],
        ]);

        $from = $data['from'] ?? null;
        $to   = $data['to'] ?? null;

        try {
            $this->classLessonService->generateLessonsForRegularClass($classGroup, $from, $to);
        } catch (DomainException|ModelNotFoundException $exception) {
            return back()
                ->withInput()
                ->withErrors([
                    'generation' => $exception->getMessage(),
                ]);
        }

        return redirect()
            ->route('class-groups.show', $classGroup)
            ->with('status', 'Lessons successfully generated.');
    }
}
