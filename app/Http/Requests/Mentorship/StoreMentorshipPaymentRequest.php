<?php

declare(strict_types=1);

namespace App\Http\Requests\Mentorship;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validates data for creating a mentorship payment.
 */
class StoreMentorshipPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Normalizes optional datetime fields.
     */
    protected function prepareForValidation(): void
    {
        if ($this->filled('paid_at') === false) {
            $this->merge(['paid_at' => null]);
        }
    }

    /**
     * Validation rules for payment creation.
     */
    public function rules(): array
    {
        return [
            'amount'  => ['required', 'numeric', 'min:0.01'],
            'paid_at' => ['nullable', 'date'],
        ];
    }
}
