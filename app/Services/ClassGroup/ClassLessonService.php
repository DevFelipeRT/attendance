<?php

declare(strict_types=1);

namespace App\Services\ClassGroup;

use App\Enums\ClassLessonStatus;
use App\Models\ClassGroup\ClassEnrollment;
use App\Models\ClassGroup\ClassGroup;
use App\Models\ClassGroup\ClassLesson;
use Carbon\Carbon;
use DomainException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class ClassLessonService
{
    /**
     * Generate scheduled lessons for a regular class group based on its weekly schedule and term.
     *
     * When no explicit range is provided, the method uses the class group term start and end dates.
     * Returns an array containing only the newly created lessons.
     *
     * @param ClassGroup|int                     $classGroup
     * @param \DateTimeInterface|string|null     $from
     * @param \DateTimeInterface|string|null     $to
     *
     * @return ClassLesson[]
     *
     * @throws ModelNotFoundException
     * @throws DomainException
     */
    public function generateLessonsForRegularClass(
        ClassGroup|int $classGroup,
        \DateTimeInterface|string|null $from = null,
        \DateTimeInterface|string|null $to = null
    ): array {
        $group = $this->resolveClassGroup($classGroup);

        if ($this->getOperationalType($group) !== 'regular') {
            throw new DomainException('Class group is not operating as a regular class.');
        }

        if (empty($group->weekly_schedule) || !is_array($group->weekly_schedule)) {
            throw new DomainException('Weekly schedule must be configured for regular class generation.');
        }

        if ($group->default_lesson_duration_minutes === null
            || (int) $group->default_lesson_duration_minutes <= 0
        ) {
            throw new DomainException('Default lesson duration must be a positive value for regular classes.');
        }

        $startDate = $this->resolveGenerationStartDate($group, $from);
        $endDate   = $this->resolveGenerationEndDate($group, $to);

        if ($startDate->gt($endDate)) {
            throw new DomainException('Generation start date must not be greater than end date.');
        }

        $created = [];

        DB::transaction(function () use ($group, $startDate, $endDate, &$created): void {
            $date = $startDate->copy();

            while ($date->lte($endDate)) {
                $weekday = $date->isoWeekday();

                foreach ($group->weekly_schedule as $slot) {
                    if (!is_array($slot)) {
                        continue;
                    }

                    $slotWeekday = (int) ($slot['weekday'] ?? 0);
                    $startTime   = $slot['start_time'] ?? null;

                    if ($slotWeekday !== $weekday || $startTime === null) {
                        continue;
                    }

                    $duration = (int) ($slot['duration_minutes'] ?? $group->default_lesson_duration_minutes);

                    if ($duration <= 0) {
                        throw new DomainException('Lesson duration must be a positive value.');
                    }

                    $lessonDate = $date->toDateString();

                    $existing = ClassLesson::where('class_group_id', $group->id)
                        ->whereDate('lesson_date', $lessonDate)
                        ->where('start_time', $startTime)
                        ->first();

                    if ($existing !== null) {
                        continue;
                    }

                    $lesson = new ClassLesson();
                    $lesson->class_group_id   = $group->id;
                    $lesson->lesson_date      = $lessonDate;
                    $lesson->start_time       = $startTime;
                    $lesson->duration_minutes = $duration;
                    $lesson->status           = ClassLessonStatus::Scheduled->value;
                    $lesson->save();

                    $created[] = $lesson;
                }

                $date->addDay();
            }
        });

        return $created;
    }

    /**
     * Create a single lesson for a class group enforcing term and mentorship duration rules.
     *
     * Expected attributes include class_group_id, lesson_date, start_time and duration_minutes.
     *
     * @param array<string,mixed> $attributes
     *
     * @throws ModelNotFoundException
     * @throws DomainException
     */
    public function createLesson(array $attributes): ClassLesson
    {
        $data = Arr::only($attributes, [
            'class_group_id',
            'lesson_date',
            'start_time',
            'duration_minutes',
            'status',
        ]);

        $classGroupId = (int) ($data['class_group_id'] ?? 0);

        if ($classGroupId <= 0) {
            throw new DomainException('A valid class_group_id is required to create a lesson.');
        }

        $group = $this->resolveClassGroup($classGroupId);

        $lessonDate      = $this->normalizeDateValue($data['lesson_date'] ?? null);
        $startTime       = (string) ($data['start_time'] ?? '');
        $durationMinutes = (int) ($data['duration_minutes'] ?? 0);

        if ($lessonDate === null) {
            throw new DomainException('Lesson date is required.');
        }

        if ($startTime === '') {
            throw new DomainException('Lesson start time is required.');
        }

        if ($durationMinutes <= 0) {
            throw new DomainException('Lesson duration_minutes must be a positive integer.');
        }

        $this->ensureLessonWithinTerm($group, $lessonDate);
        $this->ensureValidDurationForGroupType($group, $durationMinutes);

        $status = $data['status'] ?? ClassLessonStatus::Scheduled->value;

        $lesson = new ClassLesson();
        $lesson->class_group_id   = $group->id;
        $lesson->lesson_date      = $lessonDate;
        $lesson->start_time       = $startTime;
        $lesson->duration_minutes = $durationMinutes;
        $lesson->status           = $status;
        $lesson->save();

        return $lesson;
    }

    /**
     * Update an existing lesson and revalidate term and mentorship duration rules.
     *
     * The lesson can be provided as a model or as its primary key identifier.
     * The attributes array may include lesson_date, start_time, duration_minutes and status.
     *
     * @param ClassLesson|int      $lesson
     * @param array<string,mixed>  $attributes
     *
     * @throws ModelNotFoundException
     * @throws DomainException
     */
    public function updateLesson(ClassLesson|int $lesson, array $attributes): ClassLesson
    {
        $model = $this->resolveLesson($lesson);
        $group = $this->resolveClassGroup($model->class_group_id);

        $data = Arr::only($attributes, [
            'lesson_date',
            'start_time',
            'duration_minutes',
            'status',
        ]);

        $lessonDate = array_key_exists('lesson_date', $data)
            ? $this->normalizeDateValue($data['lesson_date'])
            : $model->lesson_date;

        $startTime = array_key_exists('start_time', $data)
            ? (string) $data['start_time']
            : (string) $model->start_time;

        $durationMinutes = array_key_exists('duration_minutes', $data)
            ? (int) $data['duration_minutes']
            : (int) $model->duration_minutes;

        if ($lessonDate === null) {
            throw new DomainException('Lesson date is required.');
        }

        if ($startTime === '') {
            throw new DomainException('Lesson start time is required.');
        }

        if ($durationMinutes <= 0) {
            throw new DomainException('Lesson duration_minutes must be a positive integer.');
        }

        $this->ensureLessonWithinTerm($group, $lessonDate);
        $this->ensureValidDurationForGroupType($group, $durationMinutes);

        $model->lesson_date      = $lessonDate;
        $model->start_time       = $startTime;
        $model->duration_minutes = $durationMinutes;

        if (array_key_exists('status', $data)) {
            $model->status = $data['status'];
        }

        $model->save();

        return $model;
    }

    /**
     * Cancel a lesson by setting its status to the configured cancelled value.
     *
     * @param ClassLesson|int $lesson
     *
     * @throws ModelNotFoundException
     */
    public function cancelLesson(ClassLesson|int $lesson): ClassLesson
    {
        $model = $this->resolveLesson($lesson);
        $model->status = ClassLessonStatus::Cancelled->value;
        $model->attendances()?->delete();
        $model->save();

        return $model;
    }

    public function destroyLesson(ClassLesson|int $lesson): void
    {
        $model = $this->resolveLesson($lesson);
        $model->delete();
    }

    /**
     * Resolve a class group from a model or primary key.
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
     * Resolve a lesson from a model or primary key.
     *
     * @param ClassLesson|int $lesson
     *
     * @throws ModelNotFoundException
     */
    private function resolveLesson(ClassLesson|int $lesson): ClassLesson
    {
        if ($lesson instanceof ClassLesson) {
            return $lesson;
        }

        $resolved = ClassLesson::find($lesson);

        if ($resolved === null) {
            throw new ModelNotFoundException('Class lesson not found.');
        }

        return $resolved;
    }

    /**
     * Resolve the start date for automatic generation, using term start when needed.
     *
     * @param ClassGroup                          $group
     * @param \DateTimeInterface|string|null      $from
     *
     * @throws DomainException
     */
    private function resolveGenerationStartDate(
        ClassGroup $group,
        \DateTimeInterface|string|null $from
    ): Carbon {
        if ($from !== null) {
            return $this->castToCarbon($from)->startOfDay();
        }

        if ($group->term_start_date === null) {
            throw new DomainException('Term start date is required to infer generation range.');
        }

        return Carbon::parse($group->term_start_date)->startOfDay();
    }

    /**
     * Resolve the end date for automatic generation, using term end when needed.
     *
     * @param ClassGroup                          $group
     * @param \DateTimeInterface|string|null      $to
     *
     * @throws DomainException
     */
    private function resolveGenerationEndDate(
        ClassGroup $group,
        \DateTimeInterface|string|null $to
    ): Carbon {
        if ($to !== null) {
            return $this->castToCarbon($to)->endOfDay();
        }

        if ($group->term_end_date === null) {
            throw new DomainException('Term end date is required to infer generation range.');
        }

        return Carbon::parse($group->term_end_date)->endOfDay();
    }

    /**
     * Normalize a date-like value to a YYYY-MM-DD string or null.
     *
     * @param mixed $value
     */
    private function normalizeDateValue(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if ($value instanceof Carbon) {
            return $value->toDateString();
        }

        if ($value instanceof \DateTimeInterface) {
            return Carbon::instance($value)->toDateString();
        }

        return Carbon::parse((string) $value)->toDateString();
    }

    /**
     * Convert a date-like value to a Carbon instance.
     *
     * @param \DateTimeInterface|string $value
     */
    private function castToCarbon(\DateTimeInterface|string $value): Carbon
    {
        if ($value instanceof Carbon) {
            return $value;
        }

        if ($value instanceof \DateTimeInterface) {
            return Carbon::instance($value);
        }

        return Carbon::parse($value);
    }

    /**
     * Ensure that the lesson date respects the class group term when it is defined.
     *
     * @param ClassGroup $group
     * @param string     $lessonDate
     *
     * @throws DomainException
     */
    private function ensureLessonWithinTerm(ClassGroup $group, string $lessonDate): void
    {
        if ($group->term_start_date === null && $group->term_end_date === null) {
            return;
        }

        $date  = Carbon::parse($lessonDate)->startOfDay();
        $start = $group->term_start_date !== null
            ? Carbon::parse($group->term_start_date)->startOfDay()
            : null;
        $end   = $group->term_end_date !== null
            ? Carbon::parse($group->term_end_date)->endOfDay()
            : null;

        if ($start !== null && $date->lt($start)) {
            throw new DomainException('Lesson date must not be before the class group term start date.');
        }

        if ($end !== null && $date->gt($end)) {
            throw new DomainException('Lesson date must not be after the class group term end date.');
        }
    }

    /**
     * Enforce mentorship duration rules when the class group operates as a mentorship.
     *
     * @param ClassGroup $group
     * @param int        $durationMinutes
     *
     * @throws DomainException
     */
    private function ensureValidDurationForGroupType(ClassGroup $group, int $durationMinutes): void
    {
        $type = $this->getOperationalType($group);

        if ($type !== 'mentorship') {
            return;
        }

        if ($durationMinutes % 60 !== 0) {
            throw new DomainException('Mentorship lessons must have a duration that represents whole hours.');
        }
    }

    /**
     * Determine the operational type of a class group based on current enrollments.
     *
     * Returns "none", "mentorship" or "regular".
     *
     * @param ClassGroup $group
     */
    private function getOperationalType(ClassGroup $group): string
    {
        $count = $this->getEnrollmentCount($group->id);

        if ($count === 0) {
            return 'none';
        }

        if ($count === 1) {
            return 'mentorship';
        }

        return 'regular';
    }

    /**
     * Get the number of active enrollments for a class group.
     *
     * @param int $classGroupId
     */
    private function getEnrollmentCount(int $classGroupId): int
    {
        return ClassEnrollment::where('class_group_id', $classGroupId)->count();
    }
}
