<?php

declare(strict_types=1);

namespace App\Models\ClassGroup;

use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class group model.
 *
 * Represents a regular class group with its teacher, subject, term dates,
 * default lesson duration and weekly schedule.
 */
class ClassGroup extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'subject_id',
        'teacher_id',
        'term_start_date',
        'term_end_date',
        'default_lesson_duration_minutes',
        'weekly_schedule',
        'hourly_rate',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'term_start_date'                => 'date',
        'term_end_date'                  => 'date',
        'default_lesson_duration_minutes'=> 'int',
        'weekly_schedule'                => 'array',
        'hourly_rate'                    => 'decimal:2',
    ];

    /**
     * Subject taught in this class group.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Teacher responsible for this class group.
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    /**
     * Enrollments associated with this class group.
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(ClassEnrollment::class);
    }

    /**
     * Lessons scheduled for this class group.
     */
    public function lessons(): HasMany
    {
        return $this->hasMany(ClassLesson::class);
    }
}
