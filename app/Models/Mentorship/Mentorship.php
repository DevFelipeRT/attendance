<?php

declare(strict_types=1);

namespace App\Models\Mentorship;

use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Mentorship domain model.
 *
 * Represents a one-to-one mentorship agreement between a student and a teacher,
 * with its own hourly rate and optional academic subject.
 */
class Mentorship extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'student_id',
        'teacher_id',
        'subject_id',
        'hourly_rate',
        'status',
        'started_at',
        'ended_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'hourly_rate' => 'decimal:2',
        'started_at'  => 'date',
        'ended_at'    => 'date',
    ];

    /**
     * Student participating in the mentorship.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Teacher responsible for the mentorship.
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    /**
     * Subject associated with the mentorship.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Scheduled mentorship sessions.
     */
    public function sessions(): HasMany
    {
        return $this->hasMany(MentorshipSession::class);
    }

    /**
     * Payments registered for this mentorship.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(MentorshipPayment::class);
    }

    /**
     * Hour debits generated from mentorship sessions.
     */
    public function debits(): HasMany
    {
        return $this->hasMany(MentorshipDebit::class);
    }
}
