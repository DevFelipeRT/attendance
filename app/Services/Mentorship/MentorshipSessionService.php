<?php

declare(strict_types=1);

namespace App\Services\Mentorship;

use App\Enums\ClassLessonStatus;
use App\Models\Mentorship\Mentorship;
use App\Models\Mentorship\MentorshipAttendance;
use App\Models\Mentorship\MentorshipSession;
use Carbon\Carbon;
use DomainException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

/**
 * Mentorship session service.
 *
 * Coordinates creation, update and cancellation of mentorship sessions,
 * synchronizing billing and attendance state when sessions are reset or cancelled.
 */
class MentorshipSessionService
{
    public function __construct(
        private readonly MentorshipBillingService $mentorshipBillingService,
        private readonly MentorshipAttendanceService $mentorshipAttendanceService
    ) {
    }

    /**
     * Create a new mentorship session.
     *
     * The mentorship can be provided as a model or primary key. Attributes must include session_date,
     * start_time and duration_minutes. Duration must be a positive multiple of 60 minutes.
     *
     * If the session is created already cancelled, billing is synchronized so that
     * any existing debit for this session is removed.
     *
     * @param Mentorship|int      $mentorship
     * @param array<string,mixed> $attributes
     *
     * @throws ModelNotFoundException
     * @throws DomainException
     */
    public function createSession(Mentorship|int $mentorship, array $attributes): MentorshipSession
    {
        $mentorshipModel = $this->resolveMentorship($mentorship);
        $data            = $this->prepareAttributesForCreate($mentorshipModel, $attributes);

        return DB::transaction(function () use ($data): MentorshipSession {
            $session = new MentorshipSession();
            $session->fill($data);
            $session->save();

            if ($session->status === ClassLessonStatus::Cancelled) {
                $this->mentorshipBillingService->handleSessionCancelled($session);
            }

            return $session;
        });
    }

    /**
     * Update an existing mentorship session.
     *
     * The session can be provided as a model or primary key.
     *
     * Rules for side effects:
     * - When status is updated to Cancelled, billing is synchronized and any debit
     *   for this session is removed (attendance record is kept).
     * - When status is updated from Completed back to Scheduled, the session is
     *   effectively reset: any debit is removed and the attendance record is deleted.
     *
     * @param MentorshipSession|int $session
     * @param array<string,mixed>   $attributes
     *
     * @throws ModelNotFoundException
     * @throws DomainException
     */
    public function updateSession(MentorshipSession|int $session, array $attributes): MentorshipSession
    {
        $sessionModel   = $this->resolveSession($session);
        $previousStatus = $sessionModel->status;
        $data           = $this->prepareAttributesForUpdate($sessionModel, $attributes);

        if ($data === []) {
            return $sessionModel;
        }

        return DB::transaction(function () use ($sessionModel, $previousStatus, $data): MentorshipSession {
            $sessionModel->fill($data);
            $sessionModel->save();

            if ($sessionModel->status === ClassLessonStatus::Cancelled) {
                $this->mentorshipBillingService->handleSessionCancelled($sessionModel);

                return $sessionModel;
            }

            if (
                $previousStatus === ClassLessonStatus::Completed
                && $sessionModel->status === ClassLessonStatus::Scheduled
            ) {
                $this->mentorshipBillingService->syncAttendanceDebit($sessionModel, false);

                MentorshipAttendance::where('mentorship_session_id', $sessionModel->getKey())
                    ->delete();
            }

            return $sessionModel;
        });
    }

    /**
     * Cancel a mentorship session.
     *
     * Marks the session as Cancelled and synchronizes billing so that any debit
     * associated with this session is removed.
     *
     * @param MentorshipSession|int $session
     *
     * @throws ModelNotFoundException
     */
    public function cancelSession(MentorshipSession|int $session): MentorshipSession
    {
        $sessionModel = $this->resolveSession($session);

        return DB::transaction(function () use ($sessionModel): MentorshipSession {
            $sessionModel->status = ClassLessonStatus::Cancelled;
            $sessionModel->attendance()?->delete();
            $sessionModel->save();

            $this->mentorshipBillingService->handleSessionCancelled($sessionModel);

            return $sessionModel;
        });
    }

    /**
     * Prepare attributes for mentorship session creation.
     *
     * @param array<string,mixed> $attributes
     *
     * @throws DomainException
     *
     * @return array<string,mixed>
     */
    private function prepareAttributesForCreate(Mentorship $mentorship, array $attributes): array
    {
        $sessionDate = $this->normalizeDate($attributes['session_date'] ?? null);
        $startTime   = isset($attributes['start_time']) ? trim((string) $attributes['start_time']) : '';
        $duration    = isset($attributes['duration_minutes'])
            ? (int) $attributes['duration_minutes']
            : 0;

        if ($sessionDate === null) {
            throw new DomainException('Session date is required for mentorship session creation.');
        }

        if ($startTime === '') {
            throw new DomainException('Start time is required for mentorship session creation.');
        }

        if ($duration <= 0) {
            throw new DomainException('Session duration must be a positive integer value.');
        }

        if ($duration % 60 !== 0) {
            throw new DomainException('Session duration must represent whole hours for mentorship sessions.');
        }

        $statusValue = $attributes['status'] ?? null;
        $statusEnum  = $this->normalizeStatus($statusValue);

        return [
            'mentorship_id'    => $mentorship->getKey(),
            'session_date'     => $sessionDate->toDateString(),
            'start_time'       => $startTime,
            'duration_minutes' => $duration,
            'status'           => $statusEnum,
        ];
    }

    /**
     * Prepare attributes for mentorship session update.
     *
     * @param array<string,mixed> $attributes
     *
     * @throws DomainException
     *
     * @return array<string,mixed>
     */
    private function prepareAttributesForUpdate(MentorshipSession $session, array $attributes): array
    {
        $data = [];

        if (array_key_exists('session_date', $attributes)) {
            $sessionDate = $this->normalizeDate($attributes['session_date']);

            if ($sessionDate === null) {
                throw new DomainException('Session date must be a valid date value.');
            }

            $data['session_date'] = $sessionDate->toDateString();
        }

        if (array_key_exists('start_time', $attributes)) {
            $startTime = trim((string) $attributes['start_time']);

            if ($startTime === '') {
                throw new DomainException('Start time must be a non-empty value.');
            }

            $data['start_time'] = $startTime;
        }

        if (array_key_exists('duration_minutes', $attributes)) {
            $duration = (int) $attributes['duration_minutes'];

            if ($duration <= 0) {
                throw new DomainException('Session duration must be a positive integer value.');
            }

            if ($duration % 60 !== 0) {
                throw new DomainException('Session duration must represent whole hours for mentorship sessions.');
            }

            $data['duration_minutes'] = $duration;
        }

        if (array_key_exists('status', $attributes)) {
            $data['status'] = $this->normalizeStatus($attributes['status']);
        }

        return $data;
    }

    /**
     * Resolve a mentorship instance from a model or primary key.
     *
     * @param Mentorship|int $mentorship
     *
     * @throws ModelNotFoundException
     */
    private function resolveMentorship(Mentorship|int $mentorship): Mentorship
    {
        if ($mentorship instanceof Mentorship) {
            return $mentorship;
        }

        $resolved = Mentorship::find($mentorship);

        if ($resolved === null) {
            throw new ModelNotFoundException('Mentorship not found.');
        }

        return $resolved;
    }

    /**
     * Resolve a mentorship attendance instance from a model or primary key.
     *
     * @param MentorshipAttendance|int $attendance
     *
     * @throws ModelNotFoundException
     */
    private function resolveAttendanceBySession(MentorshipSession|int $session): MentorshipAttendance
    {
        if (!$session instanceof MentorshipSession) {
            $sessionModel = MentorshipSession::find($session);
            $resolved = MentorshipAttendance::find($sessionModel->mentorship_id);
        }

        $resolved = MentorshipAttendance::find($session->mentorship_id);

        if ($resolved === null) {
            throw new ModelNotFoundException('Mentorship attendance not found.');
        }
        return $resolved;
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

    /**
     * Normalize a date-like value into a Carbon instance.
     *
     * @param mixed $value
     */
    private function normalizeDate(mixed $value): ?Carbon
    {
        if ($value === null || $value === '') {
            return null;
        }

        if ($value instanceof Carbon) {
            return $value->copy()->startOfDay();
        }

        if ($value instanceof \DateTimeInterface) {
            return Carbon::instance($value)->startOfDay();
        }

        return Carbon::parse((string) $value)->startOfDay();
    }

    /**
     * Normalize a status value into a ClassLessonStatus enum.
     *
     * @param mixed $value
     *
     * @throws DomainException
     */
    private function normalizeStatus(mixed $value): ClassLessonStatus
    {
        if ($value instanceof ClassLessonStatus) {
            return $value;
        }

        if ($value === null || $value === '') {
            return ClassLessonStatus::Scheduled;
        }

        $stringValue = (string) $value;

        try {
            return ClassLessonStatus::from($stringValue);
        } catch (\ValueError $exception) {
            throw new DomainException('Invalid status for mentorship session.', previous: $exception);
        }
    }
}
