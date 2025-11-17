<?php

declare(strict_types=1);

namespace App\Http\Requests\ClassGroup;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validate data for creating a new class group.
 */
class StoreClassGroupRequest extends FormRequest
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
            'name'                           => ['required', 'string', 'max:255'],
            'subject_id'                     => ['required', 'integer', 'exists:subjects,id'],
            'teacher_id'                     => ['required', 'integer', 'exists:teachers,id'],
            'term_start_date'                => ['nullable', 'date'],
            'term_end_date'                  => ['nullable', 'date'],
            'default_lesson_duration_minutes'=> ['nullable', 'integer', 'min:1'],
            'weekly_schedule'                => ['nullable', 'array'],
            'hourly_rate'                    => ['nullable', 'numeric', 'gt:0'],
        ];
    }
}
