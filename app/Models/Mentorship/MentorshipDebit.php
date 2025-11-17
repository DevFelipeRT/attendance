<?php

declare(strict_types=1);

namespace App\Models\Mentorship;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Mentorship debit model.
 *
 * Represents a debit of mentorship hours generated from a single mentorship
 * session.
 */
class MentorshipDebit extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'mentorship_id',
        'mentorship_session_id',
        'hours',
        'debited_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'hours'      => 'int',
        'debited_at' => 'datetime',
    ];

    /**
     * Mentorship that owns this debit.
     */
    public function mentorship(): BelongsTo
    {
        return $this->belongsTo(Mentorship::class);
    }

    /**
     * Mentorship session that originated this debit.
     */
    public function session(): BelongsTo
    {
        return $this->belongsTo(MentorshipSession::class, 'mentorship_session_id');
    }
}
