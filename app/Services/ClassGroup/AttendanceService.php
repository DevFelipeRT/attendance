<?php

declare(strict_types=1);

namespace App\Services\ClassGroup;

use App\Enums\AttendanceStatus;
use App\Enums\ClassLessonStatus;
use App\Models\ClassGroup\Attendance;
use App\Models\ClassGroup\ClassLesson;
use App\Models\Student;
use DomainException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Class attendance service.
 *
 * Coordinates registration and update of attendance records for class lessons.
 */
class AttendanceService
{
    /**
     * Register or update attendance for all students in a given lesson.
     *
     * Each item in the payload must provide a student identifier and a status code.
     * Example payload item:
     * [
     *     'student_id'        => 10,                           // or 'student' => Student|int
     *     'status'           => AttendanceStatus::Present->value,
     *     'absence_notified' => true,                         // optional, default false
     *     'extra'            => ['notes' => 'Arrived late'],  // optional
     * ]
     *
     * @param ClassLesson|int                $lesson
     * @param array<int,array<string,mixed>> $items
     *
     * @return Attendance[]
     *
     * @throws ModelNotFoundException
     * @throws DomainException
     */
    public function registerLessonAttendance(ClassLesson|int $lesson, array $items): array
    {
        $lessonModel = $this->resolveLesson($lesson);

        if ($lessonModel->status === ClassLessonStatus::Cancelled) {
            throw new DomainException('Cannot register attendance for a cancelled lesson.');
        }

        $results = [];

        foreach ($items as $row) {
            if (! is_array($row)) {
                throw new DomainException('Each attendance item must be an array.');
            }

            $student      = $row['student'] ?? null;
            $studentId    = $row['student_id'] ?? null;
            $status       = $row['status'] ?? null;
            $notified     = (bool) ($row['absence_notified'] ?? false);
            $extra        = (array) ($row['extra'] ?? []);

            if ($student !== null && $studentId === null) {
                $studentId = $this->resolveStudentId($student);
            }

            if ($studentId === null) {
                throw new DomainException('Student identifier is required for attendance registration.');
            }

            if (! is_string($status) || $status === '') {
                throw new DomainException('Attendance status is required for attendance registration.');
            }

            $this->ensureValidStatus($status);

            $attendance = $this->upsertAttendance(
                $lessonModel,
                (int) $studentId,
                $status,
                $notified,
                $extra
            );

            $results[] = $attendance;
        }

        return $results;
    }

    /**
     * Register or update attendance for a single student in a given lesson.
     *
     * @param ClassLesson|int         $lesson
     * @param Student|int             $student
     * @param string                  $status
     * @param bool                    $absenceNotified
     * @param array<string,mixed>     $extra
     *
     * @throws ModelNotFoundException
     * @throws DomainException
     */
    public function setStudentAttendance(
        ClassLesson|int $lesson,
        Student|int $student,
        string $status,
        bool $absenceNotified = false,
        array $extra = []
    ): Attendance {
        $lessonModel = $this->resolveLesson($lesson);
        $studentId   = $this->resolveStudentId($student);

        if ($lessonModel->status === ClassLessonStatus::Cancelled->value) {
            throw new DomainException('Cannot register attendance for a cancelled lesson.');
        }

        if ($status === '') {
            throw new DomainException('Attendance status is required for attendance registration.');
        }

        $this->ensureValidStatus($status);

        return $this->upsertAttendance(
            $lessonModel,
            $studentId,
            $status,
            $absenceNotified,
            $extra
        );
    }

    /**
     * Retrieve the unique attendance record for a given lesson and student, if any.
     *
     * @param ClassLesson|int $lesson
     * @param Student|int     $student
     *
     * @throws ModelNotFoundException
     */
    public function findAttendance(
        ClassLesson|int $lesson,
        Student|int $student
    ): ?Attendance {
        $lessonModel = $this->resolveLesson($lesson);
        $studentId   = $this->resolveStudentId($student);

        return Attendance::where('class_lesson_id', $lessonModel->id)
            ->where('student_id', $studentId)
            ->first();
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
            throw new DomainException('Invalid attendance status value.', previous: $e);
        }
    }

    /**
     * Create or update a single attendance row for a lesson and student.
     *
     * @param ClassLesson             $lesson
     * @param int                     $studentId
     * @param string                  $status
     * @param bool                    $absenceNotified
     * @param array<string,mixed>     $extra
     */
    private function upsertAttendance(
        ClassLesson $lesson,
        int $studentId,
        string $status,
        bool $absenceNotified,
        array $extra
    ): Attendance {
        if ($lesson->status === ClassLessonStatus::Cancelled->value) {
            throw new DomainException('Cannot register attendance for a cancelled lesson.');
        }

        /** @var Attendance|null $attendance */
        $attendance = Attendance::where('class_lesson_id', $lesson->id)
            ->where('student_id', $studentId)
            ->first();

        if ($attendance === null) {
            $attendance = new Attendance();
            $attendance->class_lesson_id = $lesson->id;
            $attendance->student_id      = $studentId;
        }

        $attendance->status           = $status;
        $attendance->absence_notified = $absenceNotified;

        if (! empty($extra)) {
            foreach ($extra as $key => $value) {
                $attendance->{$key} = $value;
            }
        }

        $attendance->save();

        $lesson->status = ClassLessonStatus::Completed;
        $lesson->save();

        return $attendance;
    }

    /**
     * Resolve a lesson instance from a model or primary key.
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
     * Resolve a student identifier from a model or primary key.
     *
     * @param Student|int $student
     *
     * @throws ModelNotFoundException
     */
    private function resolveStudentId(Student|int $student): int
    {
        if ($student instanceof Student) {
            return (int) $student->getKey();
        }

        $resolved = Student::find($student);

        if ($resolved === null) {
            throw new ModelNotFoundException('Student not found.');
        }

        return (int) $resolved->getKey();
    }
}
