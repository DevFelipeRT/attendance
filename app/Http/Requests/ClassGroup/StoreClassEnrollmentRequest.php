<?php

declare(strict_types=1);

namespace App\Http\Requests\ClassGroup;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validate data for enrolling a student in a class group.
 */
class StoreClassEnrollmentRequest extends FormRequest
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
            'student_id'  => ['required', 'integer', 'exists:students,id'],
            'enrolled_at' => ['nullable', 'date'],
        ];
    }
}
