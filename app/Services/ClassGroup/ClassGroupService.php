<?php

declare(strict_types=1);

namespace App\Services\ClassGroup;

use App\Models\ClassGroup\ClassEnrollment;
use App\Models\ClassGroup\ClassGroup;
use Carbon\Carbon;
use DomainException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Arr;

/**
 * Class group service.
 *
 * Coordinates creation and update of regular class groups, including
 * validation of identifiers and optional term configuration.
 */
class ClassGroupService
{
    /**
     * Create a new class group enforcing business rules for identifiers and term configuration.
     *
     * The attributes array must include valid subject and teacher identifiers and may include
     * optional term dates, default lesson duration, weekly schedule and hourly rate.
     *
     * @param array<string,mixed> $attributes
     *
     * @throws DomainException
     */
    public function createClassGroup(array $attributes): ClassGroup
    {
        $data = $this->prepareAttributes(null, $attributes);

        $group = new ClassGroup();
        $group->fill($data);
        $group->save();

        return $group;
    }

    /**
     * Update an existing class group with new configuration while revalidating business rules.
     *
     * The class group can be passed as an instance or as its primary key identifier.
     *
     * @param ClassGroup|int      $classGroup
     * @param array<string,mixed> $attributes
     *
     * @throws ModelNotFoundException
     * @throws DomainException
     */
    public function updateClassGroup(ClassGroup|int $classGroup, array $attributes): ClassGroup
    {
        $group = $this->resolveClassGroup($classGroup);
        $data  = $this->prepareAttributes($group, $attributes);

        $group->fill($data);
        $group->save();

        return $group;
    }

    /**
     * Verify whether a class group is eligible to operate as a regular class.
     *
     * The group must have at least two enrollments, a positive default lesson
     * duration and a non-empty weekly schedule.
     *
     * @param ClassGroup|int $classGroup
     *
     * @throws ModelNotFoundException
     */
    public function canOperateAsRegular(ClassGroup|int $classGroup): bool
    {
        $group = $this->resolveClassGroup($classGroup);

        if ($this->getEnrollmentCount($group->id) < 2) {
            return false;
        }

        if ($group->default_lesson_duration_minutes === null) {
            return false;
        }

        if ((int) $group->default_lesson_duration_minutes <= 0) {
            return false;
        }

        if (empty($group->weekly_schedule)) {
            return false;
        }

        return true;
    }

    /**
     * Resolve a class group instance from either a model or a primary key.
     *
     * @param ClassGroup|int $classGroup
     *
     * @throws ModelNotFoundException
     */
    private function resolveClassGroup(ClassGroup|int $classGroup): ClassGroup
    {
        if ($classGroup instanceof ClassGroup) {
            return $classGroup;
        }

        $resolved = ClassGroup::find($classGroup);

        if ($resolved === null) {
            throw new ModelNotFoundException('Class group not found.');
        }

        return $resolved;
    }

    /**
     * Prepare and validate attributes for creation or update of a class group.
     *
     * This method normalizes the input payload, enforces required identifiers and validates
     * the optional term dates when they are defined.
     *
     * @param ClassGroup|null     $existing
     * @param array<string,mixed> $attributes
     *
     * @return array<string,mixed>
     *
     * @throws DomainException
     */
    private function prepareAttributes(?ClassGroup $existing, array $attributes): array
    {
        $data = Arr::only($attributes, [
            'name',
            'subject_id',
            'teacher_id',
            'term_start_date',
            'term_end_date',
            'default_lesson_duration_minutes',
            'weekly_schedule',
            'hourly_rate',
        ]);

        $subjectId = $data['subject_id'] ?? $existing?->subject_id;
        $teacherId = $data['teacher_id'] ?? $existing?->teacher_id;

        if ($subjectId === null || $teacherId === null) {
            throw new DomainException('Subject and teacher must be defined for a class group.');
        }

        $data['subject_id'] = $subjectId;
        $data['teacher_id'] = $teacherId;

        $this->applyTermValidation($existing, $data);

        return $data;
    }

    /**
     * Validate and normalize term dates when a term is defined for the class group.
     *
     * Whenever either term_start_date or term_end_date is present (in new data or existing record),
     * both dates must be present and in chronological order.
     *
     * @param ClassGroup|null      $existing
     * @param array<string,mixed>  $data
     *
     * @throws DomainException
     */
    private function applyTermValidation(?ClassGroup $existing, array &$data): void
    {
        $start = $data['term_start_date'] ?? $existing?->term_start_date;
        $end   = $data['term_end_date'] ?? $existing?->term_end_date;

        if ($start === null && $end === null) {
            return;
        }

        if ($start === null || $end === null) {
            throw new DomainException(
                'Both term_start_date and term_end_date must be provided when defining a term.'
            );
        }

        $startDate = $this->normalizeDateValue($start);
        $endDate   = $this->normalizeDateValue($end);

        if ($startDate > $endDate) {
            throw new DomainException('The term_start_date must not be greater than term_end_date.');
        }

        $data['term_start_date'] = $startDate;
        $data['term_end_date']   = $endDate;
    }

    /**
     * Normalize a date-like value into a YYYY-MM-DD string.
     *
     * @param mixed $value
     */
    private function normalizeDateValue(mixed $value): string
    {
        if ($value instanceof Carbon) {
            return $value->toDateString();
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d');
        }

        return (string) $value;
    }

    /**
     * Get the number of enrollments for a given class group identifier.
     */
    private function getEnrollmentCount(int $classGroupId): int
    {
        return ClassEnrollment::where('class_group_id', $classGroupId)->count();
    }
}
