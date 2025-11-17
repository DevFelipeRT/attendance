<?php

declare(strict_types=1);

namespace App\Http\Requests\ClassGroup;

use App\Enums\ClassLessonStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validate data for updating an existing class lesson.
 */
class UpdateClassLessonRequest extends FormRequest
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
            'lesson_date'      => ['sometimes', 'date'],
            'start_time'       => ['sometimes', 'date_format:H:i'],
            'duration_minutes' => ['sometimes', 'integer', 'min:1'],
            'status'           => [
                'sometimes',
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
