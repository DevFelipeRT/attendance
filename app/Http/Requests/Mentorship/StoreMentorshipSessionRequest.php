<?php

declare(strict_types=1);

namespace App\Http\Requests\Mentorship;

use App\Enums\ClassLessonStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Form request for storing mentorship session records.
 */
class StoreMentorshipSessionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to perform this action.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules for the request.
     */
    public function rules(): array
    {
        return [
            'session_date'     => ['required', 'date'],
            'start_time'       => ['required', 'string', 'max:10'],
            'duration_minutes' => ['required', 'integer', 'min:1', 'multiple_of:60'],
            'status'           => ['nullable', Rule::enum(ClassLessonStatus::class)],
        ];
    }
}
