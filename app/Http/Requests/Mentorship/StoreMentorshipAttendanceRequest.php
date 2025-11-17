<?php

declare(strict_types=1);

namespace App\Http\Requests\Mentorship;

use App\Enums\AttendanceStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validates data for updating mentorship attendance.
 */
class StoreMentorshipAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Normalizes boolean fields and applies consistency with status.
     */
    protected function prepareForValidation(): void
    {
        $status = $this->input('status');
        $abs    = $this->boolean('absence_notified');

        if ($status !== AttendanceStatus::Absent->value) {
            $abs = false;
        }

        $this->merge([
            'absence_notified' => $abs,
        ]);
    }

    /**
     * Validation rules for attendance update.
     */
    public function rules(): array
    {
        return [
            'status'            => ['required', Rule::enum(AttendanceStatus::class)],
            'absence_notified'  => ['sometimes', 'boolean'],
        ];
    }
}
