<?php

declare(strict_types=1);

namespace App\Http\Requests\Mentorship;

use App\Enums\ClassLessonStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Form request for updating mentorship session records.
 */
class UpdateMentorshipSessionRequest extends FormRequest
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
            'session_date'     => ['sometimes', 'date'],
            'start_time'       => ['sometimes', 'string', 'max:10'],
            'duration_minutes' => ['sometimes', 'integer', 'min:1', 'multiple_of:60'],
            'status'           => ['sometimes', 'nullable', Rule::enum(ClassLessonStatus::class)],
        ];
    }
}
