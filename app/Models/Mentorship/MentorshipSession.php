<?php

declare(strict_types=1);

namespace App\Models\Mentorship;

use App\Enums\ClassLessonStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Mentorship session model.
 *
 * Represents a single scheduled mentorship session with its date, time,
 * duration and lifecycle status.
 */
class MentorshipSession extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'mentorship_id',
        'session_date',
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
        'session_date'      => 'date',
        'duration_minutes'  => 'int',
        'status'            => ClassLessonStatus::class,
    ];

    /**
     * Mentorship that owns this session.
     */
    public function mentorship(): BelongsTo
    {
        return $this->belongsTo(Mentorship::class);
    }

    /**
     * Attendance record associated with this session.
     */
    public function attendance(): HasOne
    {
        return $this->hasOne(MentorshipAttendance::class, 'mentorship_session_id');
    }
}
