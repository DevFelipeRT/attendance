<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Class lesson status values.
 *
 * Represents the lifecycle state of a concrete class lesson.
 */
enum ClassLessonStatus: string
{
    case Scheduled = 'scheduled';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
}
