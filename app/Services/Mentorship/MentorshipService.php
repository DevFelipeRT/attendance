<?php

declare(strict_types=1);

namespace App\Services\Mentorship;

use App\Models\Mentorship\Mentorship;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use DomainException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

/**
 * Mentorship service.
 *
 * Coordinates core operations for mentorships such as creation, update and
 * balance retrieval through the billing service.
 */
class MentorshipService
{
    public function __construct(
        private readonly MentorshipBillingService $billingService,
    ) {
    }

    /**
     * Create a new mentorship.
     *
     * The attributes must include student_id, teacher_id and hourly_rate.
     * Student and teacher must exist and the hourly rate must be a positive value.
     *
     * @param array<string, mixed> $attributes
     *
     * @throws ModelNotFoundException
     * @throws DomainException
     */
    public function createMentorship(array $attributes): Mentorship
    {
        $data = $this->prepareAttributesForCreate($attributes);

        return DB::transaction(function () use ($data): Mentorship {
            $mentorship = new Mentorship();
            $mentorship->fill($data);
            $mentorship->save();

            return $mentorship;
        });
    }

    /**
     * Update an existing mentorship.
     *
     * The mentorship can be provided as a model or primary key. When changing
     * student, teacher, subject or hourly_rate, the same validation rules of
     * creation are applied.
     *
     * @param Mentorship|int       $mentorship
     * @param array<string, mixed> $attributes
     *
     * @throws ModelNotFoundException
     * @throws DomainException
     */
    public function updateMentorship(Mentorship|int $mentorship, array $attributes): Mentorship
    {
        $mentorshipModel = $this->resolveMentorship($mentorship);

        $data = $this->prepareAttributesForUpdate($mentorshipModel, $attributes);

        return DB::transaction(function () use ($mentorshipModel, $data): Mentorship {
            $mentorshipModel->fill($data);
            $mentorshipModel->save();

            return $mentorshipModel;
        });
    }

    /**
     * Retrieve the mentorship hour balance for a given mentorship.
     *
     * @param Mentorship|int $mentorship
     *
     * @return array{
     *     credits_hours: float,
     *     debits_hours: float,
     *     balance_hours: float,
     * }
     *
     * @throws ModelNotFoundException
     */
    public function getBalance(Mentorship|int $mentorship): array
    {
        return $this->billingService->getBalanceForMentorship($mentorship);
    }

    /**
     * Prepare attributes for mentorship creation.
     *
     * @param array<string, mixed> $attributes
     *
     * @throws ModelNotFoundException
     * @throws DomainException
     *
     * @return array<string, mixed>
     */
    private function prepareAttributesForCreate(array $attributes): array
    {
        $studentId = isset($attributes['student_id']) ? (int) $attributes['student_id'] : 0;
        $teacherId = isset($attributes['teacher_id']) ? (int) $attributes['teacher_id'] : 0;
        $subjectId = isset($attributes['subject_id']) ? (int) $attributes['subject_id'] : null;

        if ($studentId <= 0) {
            throw new DomainException('Student id is required for mentorship creation.');
        }

        if ($teacherId <= 0) {
            throw new DomainException('Teacher id is required for mentorship creation.');
        }

        $this->ensureStudentExists($studentId);
        $this->ensureTeacherExists($teacherId);

        if ($subjectId !== null) {
            $this->ensureSubjectExists($subjectId);
        }

        if (! array_key_exists('hourly_rate', $attributes)) {
            throw new DomainException('Hourly rate is required for mentorship creation.');
        }

        $hourlyRate = (float) $attributes['hourly_rate'];

        if ($hourlyRate <= 0.0) {
            throw new DomainException('Hourly rate must be a positive value.');
        }

        $status = isset($attributes['status']) && is_string($attributes['status'])
            ? $attributes['status']
            : 'active';

        return [
            'student_id'  => $studentId,
            'teacher_id'  => $teacherId,
            'subject_id'  => $subjectId,
            'hourly_rate' => $hourlyRate,
            'status'      => $status,
            'started_at'  => $attributes['started_at'] ?? null,
            'ended_at'    => $attributes['ended_at'] ?? null,
        ];
    }

    /**
     * Prepare attributes for mentorship update.
     *
     * @param array<string, mixed> $attributes
     *
     * @throws ModelNotFoundException
     * @throws DomainException
     *
     * @return array<string, mixed>
     */
    private function prepareAttributesForUpdate(Mentorship $mentorship, array $attributes): array
    {
        $data = [];

        if (array_key_exists('student_id', $attributes)) {
            $studentId = (int) $attributes['student_id'];

            if ($studentId <= 0) {
                throw new DomainException('Student id must be a positive integer.');
            }

            $this->ensureStudentExists($studentId);
            $data['student_id'] = $studentId;
        }

        if (array_key_exists('teacher_id', $attributes)) {
            $teacherId = (int) $attributes['teacher_id'];

            if ($teacherId <= 0) {
                throw new DomainException('Teacher id must be a positive integer.');
            }

            $this->ensureTeacherExists($teacherId);
            $data['teacher_id'] = $teacherId;
        }

        if (array_key_exists('subject_id', $attributes)) {
            $subjectId = $attributes['subject_id'] !== null
                ? (int) $attributes['subject_id']
                : null;

            if ($subjectId !== null) {
                if ($subjectId <= 0) {
                    throw new DomainException('Subject id must be a positive integer when provided.');
                }

                $this->ensureSubjectExists($subjectId);
            }

            $data['subject_id'] = $subjectId;
        }

        if (array_key_exists('hourly_rate', $attributes)) {
            $hourlyRate = (float) $attributes['hourly_rate'];

            if ($hourlyRate <= 0.0) {
                throw new DomainException('Hourly rate must be a positive value.');
            }

            $data['hourly_rate'] = $hourlyRate;
        }

        if (array_key_exists('status', $attributes) && is_string($attributes['status'])) {
            $data['status'] = $attributes['status'];
        }

        if (array_key_exists('started_at', $attributes)) {
            $data['started_at'] = $attributes['started_at'];
        }

        if (array_key_exists('ended_at', $attributes)) {
            $data['ended_at'] = $attributes['ended_at'];
        }

        if ($data === []) {
            return $mentorship->getAttributes();
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
     * Ensure that a student exists for the given identifier.
     *
     * @throws ModelNotFoundException
     */
    private function ensureStudentExists(int $studentId): void
    {
        if (! Student::whereKey($studentId)->exists()) {
            throw new ModelNotFoundException('Student not found.');
        }
    }

    /**
     * Ensure that a teacher exists for the given identifier.
     *
     * @throws ModelNotFoundException
     */
    private function ensureTeacherExists(int $teacherId): void
    {
        if (! Teacher::whereKey($teacherId)->exists()) {
            throw new ModelNotFoundException('Teacher not found.');
        }
    }

    /**
     * Ensure that a subject exists for the given identifier.
     *
     * @throws ModelNotFoundException
     */
    private function ensureSubjectExists(int $subjectId): void
    {
        if (! Subject::whereKey($subjectId)->exists()) {
            throw new ModelNotFoundException('Subject not found.');
        }
    }
}
