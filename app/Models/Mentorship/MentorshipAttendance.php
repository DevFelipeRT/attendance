<?php

declare(strict_types=1);

namespace App\Models\Mentorship;

use App\Enums\AttendanceStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Mentorship attendance record.
 *
 * Represents the attendance status of a single mentorship session, including
 * whether an absence was notified in advance.
 */
class MentorshipAttendance extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'mentorship_session_id',
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
     * Mentorship session this attendance belongs to.
     */
    public function session(): BelongsTo
    {
        return $this->belongsTo(MentorshipSession::class, 'mentorship_session_id');
    }
}
