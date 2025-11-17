<?php

declare(strict_types=1);

namespace App\Http\Requests\Mentorship;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form request for storing mentorship records.
 */
class StoreMentorshipRequest extends FormRequest
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
            'student_id'  => ['required', 'integer', 'exists:students,id'],
            'teacher_id'  => ['required', 'integer', 'exists:teachers,id'],
            'subject_id'  => ['nullable', 'integer', 'exists:subjects,id'],
            'hourly_rate' => ['required', 'numeric', 'min:0.01'],
            'status'      => ['nullable', 'string', 'max:32'],
            'started_at'  => ['nullable', 'date'],
            'ended_at'    => ['nullable', 'date'],
        ];
    }
}
