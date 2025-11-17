<?php

declare(strict_types=1);

namespace App\Models\ClassGroup;

use App\Enums\AttendanceStatus;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class lesson attendance model.
 *
 * Represents the attendance status of a student in a specific class lesson.
 */
class Attendance extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'class_lesson_id',
        'student_id',
        'status',
        'absence_notified',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status'           => AttendanceStatus::class,
        'absence_notified' => 'bool',
    ];

    /**
     * Lesson this attendance belongs to.
     */
    public function classLesson(): BelongsTo
    {
        return $this->belongsTo(ClassLesson::class);
    }

    /**
     * Student for this attendance record.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
