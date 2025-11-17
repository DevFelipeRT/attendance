<?php

declare(strict_types=1);

namespace App\Http\Requests\Mentorship;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form request for updating mentorship records.
 */
class UpdateMentorshipRequest extends FormRequest
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
            'student_id'  => ['sometimes', 'integer', 'exists:students,id'],
            'teacher_id'  => ['sometimes', 'integer', 'exists:teachers,id'],
            'subject_id'  => ['sometimes', 'nullable', 'integer', 'exists:subjects,id'],
            'hourly_rate' => ['sometimes', 'numeric', 'min:0.01'],
            'status'      => ['sometimes', 'nullable', 'string', 'max:32'],
            'started_at'  => ['sometimes', 'nullable', 'date'],
            'ended_at'    => ['sometimes', 'nullable', 'date'],
        ];
    }
}
