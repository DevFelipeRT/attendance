<?php

declare(strict_types=1);

namespace App\Http\Requests\ClassGroup;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validate data for updating an existing class group.
 */
class UpdateClassGroupRequest extends FormRequest
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
            'name'                           => ['sometimes', 'required', 'string', 'max:255'],
            'subject_id'                     => ['sometimes', 'required', 'integer', 'exists:subjects,id'],
            'teacher_id'                     => ['sometimes', 'required', 'integer', 'exists:teachers,id'],
            'term_start_date'                => ['sometimes', 'nullable', 'date'],
            'term_end_date'                  => ['sometimes', 'nullable', 'date'],
            'default_lesson_duration_minutes'=> ['sometimes', 'nullable', 'integer', 'min:1'],
            'weekly_schedule'                => ['sometimes', 'nullable', 'array'],
            'hourly_rate'                    => ['sometimes', 'nullable', 'numeric', 'gt:0'],
        ];
    }
}
