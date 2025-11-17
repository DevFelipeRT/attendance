<?php

declare(strict_types=1);

namespace App\Http\Requests\ClassGroup;

use App\Enums\ClassLessonStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validate data for creating a new class lesson.
 */
class StoreClassLessonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'lesson_date'      => ['required', 'date'],
            'start_time'       => ['required', 'date_format:H:i'],
            'duration_minutes' => ['required', 'integer', 'min:1'],
            'status'           => [
                'nullable',
                'string',
                Rule::in([
                    ClassLessonStatus::Scheduled->value,
                    ClassLessonStatus::Completed->value,
                    ClassLessonStatus::Cancelled->value,
                ]),
            ],
        ];
    }
}
