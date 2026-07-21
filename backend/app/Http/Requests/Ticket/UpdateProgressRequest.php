<?php

namespace App\Http\Requests\Ticket;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProgressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('ticket')) ?? false;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            // Progress hanya kelipatan 25 (FR-12).
            'progress' => ['required', 'integer', Rule::in([0, 25, 50, 75, 100])],
        ];
    }

    public function messages(): array
    {
        return ['progress.in' => 'Progress hanya boleh 0, 25, 50, 75, atau 100.'];
    }
}
