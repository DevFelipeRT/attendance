<?php

declare(strict_types=1);

namespace App\Models\ClassGroup;

use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class enrollment model.
 *
 * Represents the enrollment of a student in a regular class group.
 */
class ClassEnrollment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'class_group_id',
        'student_id',
        'enrolled_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'enrolled_at' => 'datetime',
    ];

    /**
     * Class group for this enrollment.
     */
    public function classGroup(): BelongsTo
    {
        return $this->belongsTo(ClassGroup::class);
    }

    /**
     * Student for this enrollment.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
