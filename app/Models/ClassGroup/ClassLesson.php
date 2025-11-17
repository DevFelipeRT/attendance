<?php

declare(strict_types=1);

namespace App\Models\ClassGroup;

use App\Enums\ClassLessonStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class lesson model.
 *
 * Represents a single scheduled lesson for a regular class group.
 */
class ClassLesson extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'class_group_id',
        'lesson_date',
        'start_time',
        'duration_minutes',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'lesson_date'     => 'date',
        'duration_minutes'=> 'int',
        'status'          => ClassLessonStatus::class,
    ];

    /**
     * Class group that owns this lesson.
     */
    public function classGroup(): BelongsTo
    {
        return $this->belongsTo(ClassGroup::class);
    }

    /**
     * Attendance records for this lesson.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }
}
