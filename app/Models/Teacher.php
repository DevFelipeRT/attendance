<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\ClassGroup\ClassGroup;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Teacher domain model.
 *
 * Represents an instructor responsible for one or more class groups
 * and paired to a single user account.
 */
class Teacher extends Model
{
    use HasFactory;

    /**
     * Attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'user_id',
    ];

    /**
     * User account associated with this teacher.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Class groups assigned to this teacher.
     */
    public function classGroups(): HasMany
    {
        return $this->hasMany(ClassGroup::class);
    }
}
