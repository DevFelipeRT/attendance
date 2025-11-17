<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Attendance status values.
 *
 * Represents the attendance state of a student for a class lesson.
 */
enum AttendanceStatus: string
{
    case Present = 'present';
    case Late    = 'late';
    case Absent  = 'absent';
}
