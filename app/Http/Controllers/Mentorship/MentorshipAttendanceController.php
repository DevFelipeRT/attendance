<?php

declare(strict_types=1);

namespace App\Http\Controllers\Mentorship;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mentorship\StoreMentorshipAttendanceRequest;
use App\Models\Mentorship\Mentorship;
use App\Models\Mentorship\MentorshipSession;
use App\Services\Mentorship\MentorshipAttendanceService;
use DomainException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * HTTP controller for managing attendance for a mentorship session.
 */
class MentorshipAttendanceController extends Controller
{
    public function __construct(
        private readonly MentorshipAttendanceService $mentorshipAttendanceService,
    ) {
    }

    /**
     * Show the attendance form for a mentorship session.
     */
    public function edit(Mentorship $mentorship, MentorshipSession $session): View
    {
        if ((int) $session->mentorship_id !== (int) $mentorship->id) {
            abort(404);
        }

        $mentorship->load(['student', 'teacher', 'subject']);
        $session->load(['mentorship']);

        $attendance = $this->mentorshipAttendanceService->findAttendance($session);

        return view('mentorships.attendance.edit', [
            'mentorship' => $mentorship,
            'student'    => $mentorship->student,
            'teacher'    => $mentorship->teacher,
            'subject'    => $mentorship->subject,
            'session'    => $session,
            'attendance' => $attendance,
        ]);
    }

    /**
     * Persist attendance for a mentorship session.
     *
     * Expected payload:
     * - status
     * - absence_notified (optional, boolean)
     */
    public function update(
        StoreMentorshipAttendanceRequest $request,
        Mentorship $mentorship,
        MentorshipSession $session
    ): RedirectResponse {
        if ((int) $session->mentorship_id !== (int) $mentorship->id) {
            abort(404);
        }

        $data = $request->validated();

        $status          = $data['status'];
        $absenceNotified = (bool) ($data['absence_notified'] ?? false);

        try {
            $this->mentorshipAttendanceService->setSessionAttendance(
                $session,
                $status,
                $absenceNotified
            );
        } catch (DomainException|ModelNotFoundException $exception) {
            return back()
                ->withInput()
                ->withErrors([
                    'attendance' => $exception->getMessage(),
                ]);
        }

        return redirect()
            ->route('mentorships.sessions.attendance.edit', [$mentorship, $session])
            ->with('status', 'Mentorship attendance successfully saved.');
    }
}
