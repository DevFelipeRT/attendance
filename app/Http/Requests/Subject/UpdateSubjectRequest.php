<?php

declare(strict_types=1);

namespace App\Http\Requests\Subject;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validate data for updating an existing subject.
 */
class UpdateSubjectRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
        ];
    }
}
