<?php

namespace App\Http\Requests\Ticket;

use App\Enums\Priority;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class StoreTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', \App\Models\Ticket::class) ?? false;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'division_id' => ['required', Rule::exists('divisions', 'id')->where('is_active', true)],
            'sender_name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', Rule::exists('categories', 'id')->where('is_active', true)],
            'priority' => ['required', new Enum(Priority::class)],
            'location' => ['nullable', 'string', 'max:255'],
            'deadline' => ['nullable', 'date', 'after:now'],
            'attachments' => ['sometimes', 'array', 'max:10'],
            'attachments.*' => ['file', 'max:10240'], // 10 MB / file
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul wajib diisi.',
            'description.required' => 'Deskripsi wajib diisi.',
            'division_id.exists' => 'Divisi tidak valid.',
            'category_id.exists' => 'Kategori tidak valid.',
            'deadline.after' => 'Deadline harus setelah waktu sekarang.',
        ];
    }
}
