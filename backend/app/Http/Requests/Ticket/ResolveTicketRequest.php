<?php

namespace App\Http\Requests\Ticket;

use App\Enums\ResolutionOutcome;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class ResolveTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('ticket')) ?? false;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $rejected = $this->input('outcome') === ResolutionOutcome::Rejected->value;

        return [
            'outcome' => ['required', new Enum(ResolutionOutcome::class)],
            // Jika "Tidak Bisa Dikerjakan", berita acara wajib (FR-13).
            'reason' => [$rejected ? 'required' : 'nullable', 'string'],
            'berita_acara' => [$rejected ? 'required' : 'nullable', 'string'],
            'recommendation' => [$rejected ? 'required' : 'nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'reason.required' => 'Alasan wajib diisi saat menolak tiket.',
            'berita_acara.required' => 'Berita acara wajib diisi saat menolak tiket.',
            'recommendation.required' => 'Rekomendasi wajib diisi saat menolak tiket.',
        ];
    }
}
