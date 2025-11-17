<?php

declare(strict_types=1);

namespace App\Http\Requests\ClassGroup;

use App\Enums\AttendanceStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validate data for registering attendance for a lesson.
 */
class StoreAttendanceRequest extends FormRequest
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
            'items'                       => ['required', 'array', 'min:1'],
            'items.*.student_id'          => ['required', 'integer', 'exists:students,id'],
            'items.*.status'              => [
                'required',
                'string',
                Rule::in([
                    AttendanceStatus::Present->value,
                    AttendanceStatus::Late->value,
                    AttendanceStatus::Absent->value,
                ]),
            ],
            'items.*.absence_notified'    => ['sometimes', 'boolean'],
            'items.*.extra'               => ['sometimes', 'array'],
        ];
    }
}
