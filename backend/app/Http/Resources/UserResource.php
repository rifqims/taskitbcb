<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Bentuk output JSON user yang konsisten (tanpa membocorkan password/hash).
 *
 * @mixin \App\Models\User
 */
class UserResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role->value,
            'role_label' => $this->role->label(),
            'division' => $this->whenLoaded('division', fn () => [
                'id' => $this->division?->id,
                'name' => $this->division?->name,
            ]),
            'division_id' => $this->division_id,
            'phone' => $this->phone,
            'job_title' => $this->job_title,
            'avatar_path' => $this->avatar_path,
            'is_active' => $this->is_active,
        ];
    }
}
