<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\ClassGroup\ClassGroup;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Subject domain model.
 *
 * Represents an academic subject taught across one or more class groups.
 */
class Subject extends Model
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
     * Class groups associated with the subject.
     */
    public function classGroups(): HasMany
    {
        return $this->hasMany(ClassGroup::class);
    }
}
