<?php

declare(strict_types=1);

namespace App\Services\ClassGroup;

use App\Models\ClassGroup\ClassEnrollment;
use App\Models\ClassGroup\ClassGroup;
use App\Models\Student;
use Carbon\Carbon;
use DomainException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Class enrollment service.
 *
 * Coordinates enrollment and unenrollment of students in regular class groups,
 * enforcing uniqueness and providing query helpers.
 */
class ClassEnrollmentService
{
    /**
     * Enroll a student into a class group enforcing uniqueness and business rules.
     *
     * The class group and student can be provided as models or identifiers.
     * When no enrollment date is provided, the current timestamp is used.
     *
     * @param ClassGroup|int                 $classGroup
     * @param Student|int                    $student
     * @param \DateTimeInterface|string|null $enrolledAt
     *
     * @throws ModelNotFoundException
     * @throws DomainException
     */
    public function enrollStudent(
        ClassGroup|int $classGroup,
        Student|int $student,
        \DateTimeInterface|string|null $enrolledAt = null
    ): ClassEnrollment {
        $classGroupId = $this->resolveClassGroupId($classGroup);
        $studentId    = $this->resolveStudentId($student);

        if ($this->isEnrolled($classGroupId, $studentId)) {
            throw new DomainException('Student is already enrolled in this class group.');
        }

        $enrollment = new ClassEnrollment();
        $enrollment->class_group_id = $classGroupId;
        $enrollment->student_id     = $studentId;
        $enrollment->enrolled_at    = $this->normalizeEnrollmentDate($enrolledAt);

        $enrollment->save();

        return $enrollment;
    }

    /**
     * Unenroll a student by deleting the corresponding enrollment record.
     *
     * The enrollment can be passed as a model or as its primary key identifier.
     *
     * @param ClassEnrollment|int $enrollment
     *
     * @throws ModelNotFoundException
     */
    public function unenrollStudent(ClassEnrollment|int $enrollment): void
    {
        $model = $this->resolveEnrollment($enrollment);
        $model->delete();
    }

    /**
     * Get the number of active enrollments for a given class group.
     *
     * @param ClassGroup|int $classGroup
     *
     * @throws ModelNotFoundException
     */
    public function getEnrollmentCount(ClassGroup|int $classGroup): int
    {
        $classGroupId = $this->resolveClassGroupId($classGroup);

        return ClassEnrollment::where('class_group_id', $classGroupId)->count();
    }

    /**
     * Retrieve the enrollment record for a student in a given class group, if any.
     *
     * @param ClassGroup|int $classGroup
     * @param Student|int    $student
     *
     * @throws ModelNotFoundException
     */
    public function findEnrollment(
        ClassGroup|int $classGroup,
        Student|int $student
    ): ?ClassEnrollment {
        $classGroupId = $this->resolveClassGroupId($classGroup);
        $studentId    = $this->resolveStudentId($student);

        return ClassEnrollment::where('class_group_id', $classGroupId)
            ->where('student_id', $studentId)
            ->first();
    }

    /**
     * Check whether a student is currently enrolled in a given class group.
     *
     * @param ClassGroup|int $classGroup
     * @param Student|int    $student
     *
     * @throws ModelNotFoundException
     */
    public function isStudentEnrolled(
        ClassGroup|int $classGroup,
        Student|int $student
    ): bool {
        $classGroupId = $this->resolveClassGroupId($classGroup);
        $studentId    = $this->resolveStudentId($student);

        return $this->isEnrolled($classGroupId, $studentId);
    }

    /**
     * Resolve a class group identifier from a model or its primary key.
     *
     * @param ClassGroup|int $classGroup
     *
     * @throws ModelNotFoundException
     */
    private function resolveClassGroupId(ClassGroup|int $classGroup): int
    {
        if ($classGroup instanceof ClassGroup) {
            return (int) $classGroup->getKey();
        }

        $model = ClassGroup::find($classGroup);

        if ($model === null) {
            throw new ModelNotFoundException('Class group not found.');
        }

        return (int) $model->getKey();
    }

    /**
     * Resolve a student identifier from a model or its primary key.
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

        $model = Student::find($student);

        if ($model === null) {
            throw new ModelNotFoundException('Student not found.');
        }

        return (int) $model->getKey();
    }

    /**
     * Resolve an enrollment model from an instance or its primary key.
     *
     * @param ClassEnrollment|int $enrollment
     *
     * @throws ModelNotFoundException
     */
    private function resolveEnrollment(ClassEnrollment|int $enrollment): ClassEnrollment
    {
        if ($enrollment instanceof ClassEnrollment) {
            return $enrollment;
        }

        $model = ClassEnrollment::find($enrollment);

        if ($model === null) {
            throw new ModelNotFoundException('Class enrollment not found.');
        }

        return $model;
    }

    /**
     * Normalize the enrollment date to a Carbon instance.
     *
     * When the value is null the current timestamp is returned.
     *
     * @param \DateTimeInterface|string|null $value
     */
    private function normalizeEnrollmentDate(
        \DateTimeInterface|string|null $value
    ): Carbon {
        if ($value === null) {
            return Carbon::now();
        }

        if ($value instanceof \DateTimeInterface) {
            return Carbon::instance($value);
        }

        return Carbon::parse($value);
    }

    /**
     * Check if an enrollment already exists for the given class group and student identifiers.
     *
     * @param int $classGroupId
     * @param int $studentId
     */
    private function isEnrolled(int $classGroupId, int $studentId): bool
    {
        return ClassEnrollment::where('class_group_id', $classGroupId)
            ->where('student_id', $studentId)
            ->exists();
    }
}
