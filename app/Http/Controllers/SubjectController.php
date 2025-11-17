<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Subject\StoreSubjectRequest;
use App\Http\Requests\Subject\UpdateSubjectRequest;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * HTTP controller for managing subjects.
 */
class SubjectController extends Controller
{
    /**
     * Display a listing of subjects.
     */
    public function index(): View
    {
        $subjects = Subject::query()
            ->orderBy('name')
            ->get();

        return view('subjects.index', [
            'subjects' => $subjects,
        ]);
    }

    /**
     * Show the form for creating a new subject.
     */
    public function create(): View
    {
        return view('subjects.create');
    }

    /**
     * Store a newly created subject in storage.
     */
    public function store(StoreSubjectRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $subject = Subject::query()->create($data);

        return redirect()
            ->route('subjects.edit', $subject)
            ->with('status', 'Subject successfully created.');
    }

    /**
     * Show the form for editing the specified subject.
     */
    public function edit(Subject $subject): View
    {
        return view('subjects.edit', [
            'subject' => $subject,
        ]);
    }

    /**
     * Update the specified subject in storage.
     */
    public function update(UpdateSubjectRequest $request, Subject $subject): RedirectResponse
    {
        $data = $request->validated();

        $subject->update($data);

        return redirect()
            ->route('subjects.edit', $subject)
            ->with('status', 'Subject successfully updated.');
    }

    /**
     * Remove the specified subject from storage.
     */
    public function destroy(Subject $subject): RedirectResponse
    {
        $subject->delete();

        return redirect()
            ->route('subjects.index')
            ->with('status', 'Subject successfully deleted.');
    }
}
