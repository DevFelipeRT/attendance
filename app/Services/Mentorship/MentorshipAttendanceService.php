<?php

declare(strict_types=1);

namespace App\Services\Mentorship;

use App\Enums\AttendanceStatus;
use App\Enums\ClassLessonStatus;
use App\Models\Mentorship\MentorshipAttendance;
use App\Models\Mentorship\MentorshipSession;
use DomainException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Mentorship attendance service.
 *
 * Coordinates registration of attendance for mentorship sessions and
 * synchronizes hour debits via the MentorshipBillingService when applicable.
 */
class MentorshipAttendanceService
{
    public function __construct(
        private readonly MentorshipBillingService $mentorshipBillingService,
    ) {
    }

    /**
     * Register or update attendance for a single mentorship session.
     *
     * Rules for debit:
     * - Never debit when the session is cancelled.
     * - Debit when:
     *   - status = Present or Late; or
     *   - status = Absent and absence_notified = false.
     *
     * After this method runs, the debit state for the session is consistent
     * with the current attendance: if attendance requires debit, a debit
     * exists; otherwise it is removed.
     *
     * @param MentorshipSession|int $session
     * @param string                $status
     * @param bool                  $absenceNotified
     * @param array<string,mixed>   $extra
     *
     * @throws ModelNotFoundException
     * @throws DomainException
     */
    public function setSessionAttendance(
        MentorshipSession|int $session,
        string $status,
        bool $absenceNotified = false,
        array $extra = []
    ): MentorshipAttendance {
        $sessionModel = $this->resolveSession($session);

        if ($status === '') {
            throw new DomainException('Attendance status is required for mentorship attendance registration.');
        }

        $this->ensureValidStatus($status);

        $attendance = $this->upsertAttendance(
            $sessionModel,
            $status,
            $absenceNotified,
            $extra
        );

        $shouldDebit = $this->shouldDebitForAttendance($sessionModel, $attendance);

        $this->mentorshipBillingService->syncAttendanceDebit($sessionModel, $shouldDebit);

        $this->markSessionCompleted($sessionModel);

        return $attendance;
    }

    /**
     * Retrieve the unique attendance record for a given mentorship session, if any.
     *
     * @param MentorshipSession|int $session
     *
     * @throws ModelNotFoundException
     */
    public function findAttendance(
        MentorshipSession|int $session
    ): ?MentorshipAttendance {
        $sessionModel = $this->resolveSession($session);

        return MentorshipAttendance::where('mentorship_session_id', $sessionModel->id)->first();
    }

    /**
     * Handle a mentorship session cancellation.
     *
     * @param MentorshipSession   $session
     * @param MentorshipAttendance $attendance
     *
     * @throws ModelNotFoundException
     * @throws DomainException
     */
    public function handleSessionCancelled(MentorshipSession $session, MentorshipAttendance $attendance): void
    {
        if ($attendance->mentorship_session_id !== $session->id) {
            throw new DomainException('Unauthorized session cancellation.');
        }

        if ($session->status !== ClassLessonStatus::Cancelled) {
            return;
        }

        $attendance->delete();
    }

    /**
     * Ensure that a given status string maps to a valid AttendanceStatus enum value.
     *
     * @throws DomainException
     */
    private function ensureValidStatus(string $status): void
    {
        try {
            AttendanceStatus::from($status);
        } catch (\ValueError $e) {
            throw new DomainException('Invalid attendance status value for mentorship.', previous: $e);
        }
    }

    /**
     * Create or update the attendance row for a mentorship session.
     *
     * This method only persists the attendance record itself. The session
     * status lifecycle is handled separately.
     *
     * @param MentorshipSession   $session
     * @param string              $status
     * @param bool                $absenceNotified
     * @param array<string,mixed> $extra
     */
    private function upsertAttendance(
        MentorshipSession $session,
        string $status,
        bool $absenceNotified,
        array $extra
    ): MentorshipAttendance {
        /** @var MentorshipAttendance|null $attendance */
        $attendance = MentorshipAttendance::where('mentorship_session_id', $session->id)->first();

        if ($attendance === null) {
            $attendance = new MentorshipAttendance();
            $attendance->mentorship_session_id = $session->id;
        }

        $attendance->status           = $status;
        $attendance->absence_notified = $absenceNotified;

        if (! empty($extra)) {
            foreach ($extra as $key => $value) {
                $attendance->{$key} = $value;
            }
        }

        $attendance->save();

        return $attendance;
    }

    /**
     * Determine whether the current attendance requires a mentorship debit.
     */
    private function shouldDebitForAttendance(
        MentorshipSession $session,
        MentorshipAttendance $attendance
    ): bool {
        // Session::$status is cast to ClassLessonStatus, so compare to the enum itself.
        if ($session->status === ClassLessonStatus::Cancelled) {
            return false;
        }

        $statusEnum = $this->normalizeAttendanceStatus($attendance->status);

        return match ($statusEnum) {
            AttendanceStatus::Present,
            AttendanceStatus::Late   => true,
            AttendanceStatus::Absent => ! (bool) $attendance->absence_notified,
        };
    }

    /**
     * Normalize a status value that may already be an enum instance or a raw string.
     *
     * @param AttendanceStatus|string $status
     */
    private function normalizeAttendanceStatus(AttendanceStatus|string $status): AttendanceStatus
    {
        if ($status instanceof AttendanceStatus) {
            return $status;
        }

        return AttendanceStatus::from($status);
    }

    /**
     * Mark the session as completed after attendance is recorded,
     * unless it is cancelled.
     */
    private function markSessionCompleted(MentorshipSession $session): void
    {
        if ($session->status === ClassLessonStatus::Cancelled) {
            return;
        }

        $session->status = ClassLessonStatus::Completed;
        $session->save();
    }

    /**
     * Resolve a mentorship session instance from a model or primary key.
     *
     * @param MentorshipSession|int $session
     *
     * @throws ModelNotFoundException
     */
    private function resolveSession(MentorshipSession|int $session): MentorshipSession
    {
        if ($session instanceof MentorshipSession) {
            return $session;
        }

        $resolved = MentorshipSession::find($session);

        if ($resolved === null) {
            throw new ModelNotFoundException('Mentorship session not found.');
        }

        return $resolved;
    }
}
