<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\ClassGroup\Attendance;
use App\Models\ClassGroup\ClassEnrollment;
use App\Models\ClassGroup\ClassGroup;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Student domain model.
 *
 * Represents a learner enrolled in one or more class groups,
 * including regular groups and one-to-one mentorship groups.
 */
class Student extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
    ];

    /**
     * Class enrollments associated with the student.
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(ClassEnrollment::class);
    }

    /**
     * Class groups in which the student is enrolled.
     */
    public function classGroups(): BelongsToMany
    {
        return $this->belongsToMany(
            ClassGroup::class,
            'class_enrollments',
            'student_id',
            'class_group_id'
        )->withTimestamps();
    }

    /**
     * Attendance records for the student.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }
}
