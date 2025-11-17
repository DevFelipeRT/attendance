<?php

declare(strict_types=1);

namespace App\Models\Mentorship;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Mentorship payment model.
 *
 * Represents a monetary payment registered for a specific mentorship and the
 * corresponding number of credited hours.
 */
class MentorshipPayment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'mentorship_id',
        'amount',
        'hours',
        'paid_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount'  => 'decimal:2',
        'hours'   => 'int',
        'paid_at' => 'datetime',
    ];

    /**
     * Mentorship that owns this payment.
     */
    public function mentorship(): BelongsTo
    {
        return $this->belongsTo(Mentorship::class);
    }
}
