<?php

namespace App\Http\Requests\Ticket;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Boleh chat jika boleh melihat tiket (peserta: pembuat/assignee/IT/admin).
        return $this->user()?->can('view', $this->route('ticket')) ?? false;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'body' => ['required_without:attachments', 'nullable', 'string', 'max:5000'],
            'attachments' => ['sometimes', 'array', 'max:10'],
            'attachments.*' => ['file', 'max:20480'], // 20 MB / file
            'mentions' => ['sometimes', 'array'],
            'mentions.*' => [Rule::exists('users', 'id')],
        ];
    }
}
