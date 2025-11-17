<?php

declare(strict_types=1);

namespace App\Http\Controllers\Mentorship;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mentorship\StoreMentorshipRequest;
use App\Http\Requests\Mentorship\UpdateMentorshipRequest;
use App\Models\Mentorship\Mentorship;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Services\Mentorship\MentorshipService;
use DomainException;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;

/**
 * Mentorship controller.
 *
 * Exposes CRUD operations for mentorships and delegates business rules
 * to the MentorshipService.
 */
class MentorshipController extends Controller
{
    public function __construct(
        private readonly MentorshipService $mentorshipService,
    ) {
    }

    /**
     * Display a listing of mentorships.
     */
    public function index(): View
    {
        $mentorships = Mentorship::with(['student', 'teacher', 'subject'])->get();

        return view('mentorships.index', [
            'items' => $mentorships,
        ]);
    }

    /**
     * Show the form for creating a new mentorship.
     */
    public function create(): View
    {
        $students = Student::orderBy('name')->get();
        $teachers = Teacher::orderBy('name')->get();
        $subjects = Subject::orderBy('name')->get();

        return view('mentorships.create', [
            'students' => $students,
            'teachers' => $teachers,
            'subjects' => $subjects,
        ]);
    }

    /**
     * Store a newly created mentorship in storage.
     */
    public function store(StoreMentorshipRequest $request): RedirectResponse
    {
        $data = $request->validated();

        try {
            $mentorship = $this->mentorshipService->createMentorship($data);
        } catch (DomainException|ModelNotFoundException $e) {
            return back()
                ->withInput()
                ->withErrors([
                    'mentorship' => $e->getMessage(),
                ]);
        }

        return redirect()
            ->route('mentorships.show', $mentorship)
            ->with('status', __('Mentorship created successfully.'));
    }

    /**
     * Display the specified mentorship with related aggregates.
     *
     * Includes payments, debits, and the ordered list of sessions plus
     * the computed balance overview to be rendered in the unified details view.
     */
    public function show(Mentorship $mentorship): View
    {
        $mentorship->load([
            'student',
            'teacher',
            'subject',
            'payments',
            'debits',
        ]);

        $sessions = $mentorship->sessions()
            ->orderBy('session_date')
            ->orderBy('start_time')
            ->get();

        $balance = $this->mentorshipService->getBalance($mentorship);

        return view('mentorships.show', [
            'mentorship' => $mentorship,
            'sessions'   => $sessions,
            'balance'    => $balance,
        ]);
    }

    /**
     * Show the form for editing the specified mentorship.
     */
    public function edit(Mentorship $mentorship): View
    {
        $mentorship->load(['student', 'teacher', 'subject']);

        $students = Student::orderBy('name')->get();
        $teachers = Teacher::orderBy('name')->get();
        $subjects = Subject::orderBy('name')->get();

        return view('mentorships.edit', [
            'mentorship' => $mentorship,
            'students'   => $students,
            'teachers'   => $teachers,
            'subjects'   => $subjects,
        ]);
    }

    /**
     * Update the specified mentorship in storage.
     */
    public function update(UpdateMentorshipRequest $request, Mentorship $mentorship): RedirectResponse
    {
        $data = $request->validated();

        try {
            $this->mentorshipService->updateMentorship($mentorship, $data);
        } catch (DomainException|ModelNotFoundException $e) {
            return back()
                ->withInput()
                ->withErrors([
                    'mentorship' => $e->getMessage(),
                ]);
        }

        return redirect()
            ->route('mentorships.show', $mentorship)
            ->with('status', __('Mentorship updated successfully.'));
    }

    /**
     * Remove the specified mentorship from storage.
     */
    public function destroy(Mentorship $mentorship): RedirectResponse
    {
        $mentorship->delete();

        return redirect()
            ->route('mentorships.index')
            ->with('status', __('Mentorship deleted successfully.'));
    }
}
