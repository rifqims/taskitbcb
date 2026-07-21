<?php

namespace App\Http\Requests\Ticket;

use App\Enums\TicketStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ChangeStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('ticket')) ?? false;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            // Hanya transisi non-terminal lewat endpoint ini; Selesai/Ditolak lewat /resolve.
            'status' => ['required', Rule::in([
                TicketStatus::SedangDikerjakan->value,
                TicketStatus::Pending->value,
            ])],
        ];
    }
}
