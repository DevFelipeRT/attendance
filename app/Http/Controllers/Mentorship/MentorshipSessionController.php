<?php

declare(strict_types=1);

namespace App\Http\Controllers\Mentorship;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mentorship\StoreMentorshipSessionRequest;
use App\Http\Requests\Mentorship\UpdateMentorshipSessionRequest;
use App\Models\Mentorship\Mentorship;
use App\Models\Mentorship\MentorshipSession;
use App\Services\Mentorship\MentorshipSessionService;
use DomainException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * HTTP controller for managing mentorship sessions.
 *
 * Session listing is now embedded in the mentorship details page.
 */
class MentorshipSessionController extends Controller
{
    public function __construct(
        private readonly MentorshipSessionService $mentorshipSessionService,
    ) {
    }

    /**
     * Redirect session listing to the mentorship details page.
     */
    public function index(Mentorship $mentorship): RedirectResponse
    {
        return redirect()->route('mentorships.show', $mentorship);
    }

    /**
     * Show the form for creating a new session for the given mentorship.
     */
    public function create(Mentorship $mentorship): View
    {
        $mentorship->load(['student', 'teacher', 'subject']);

        return view('mentorships.sessions.create', [
            'mentorship' => $mentorship,
            'student'    => $mentorship->student,
            'teacher'    => $mentorship->teacher,
            'subject'    => $mentorship->subject,
        ]);
    }

    /**
     * Store a newly created session for the given mentorship.
     */
    public function store(
        StoreMentorshipSessionRequest $request,
        Mentorship $mentorship
    ): RedirectResponse {
        $data = $request->validated();

        try {
            $this->mentorshipSessionService->createSession($mentorship, $data);
        } catch (DomainException|ModelNotFoundException $exception) {
            return back()
                ->withInput()
                ->withErrors([
                    'session' => $exception->getMessage(),
                ]);
        }

        return redirect()
            ->route('mentorships.show', $mentorship)
            ->with('status', 'Mentorship session successfully created.');
    }

    /**
     * Redirect the specified session to the mentorship details page.
     */
    public function show(Mentorship $mentorship, MentorshipSession $session): RedirectResponse
    {
        if ((int) $session->mentorship_id !== (int) $mentorship->id) {
            abort(404);
        }

        return redirect()->route('mentorships.show', $mentorship);
    }

    /**
     * Show the form for editing the specified mentorship session.
     */
    public function edit(Mentorship $mentorship, MentorshipSession $session): View
    {
        if ((int) $session->mentorship_id !== (int) $mentorship->id) {
            abort(404);
        }

        $mentorship->load(['student', 'teacher', 'subject']);

        return view('mentorships.sessions.edit', [
            'mentorship' => $mentorship,
            'student'    => $mentorship->student,
            'teacher'    => $mentorship->teacher,
            'subject'    => $mentorship->subject,
            'session'    => $session,
        ]);
    }

    /**
     * Update the specified mentorship session.
     */
    public function update(
        UpdateMentorshipSessionRequest $request,
        Mentorship $mentorship,
        MentorshipSession $session
    ): RedirectResponse {
        if ((int) $session->mentorship_id !== (int) $mentorship->id) {
            abort(404);
        }

        $data = $request->validated();

        try {
            $this->mentorshipSessionService->updateSession($session, $data);
        } catch (DomainException|ModelNotFoundException $exception) {
            return back()
                ->withInput()
                ->withErrors([
                    'session' => $exception->getMessage(),
                ]);
        }

        return redirect()
            ->route('mentorships.show', $mentorship)
            ->with('status', 'Mentorship session successfully updated.');
    }

    /**
     * Cancel the specified mentorship session.
     */
    public function cancel(
        Mentorship $mentorship,
        MentorshipSession $session
    ): RedirectResponse {
        if ((int) $session->mentorship_id !== (int) $mentorship->id) {
            abort(404);
        }

        try {
            $this->mentorshipSessionService->cancelSession($session);
        } catch (DomainException|ModelNotFoundException $exception) {
            return back()
                ->withErrors([
                    'session' => $exception->getMessage(),
                ]);
        }

        return redirect()
            ->route('mentorships.show', $mentorship)
            ->with('status', 'Mentorship session successfully cancelled.');
    }

    /**
     * Remove the specified mentorship session from storage.
     */
    public function destroy(
        Mentorship $mentorship,
        MentorshipSession $session
    ): RedirectResponse {
        if ((int) $session->mentorship_id !== (int) $mentorship->id) {
            abort(404);
        }

        $session->delete();

        return redirect()
            ->route('mentorships.show', $mentorship)
            ->with('status', 'Mentorship session successfully deleted.');
    }
}
