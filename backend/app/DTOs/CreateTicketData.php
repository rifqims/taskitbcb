<?php

namespace App\DTOs;

use App\Enums\Priority;

/**
 * Objek transfer data pembuatan tiket (data eksplisit & bertipe antar-lapisan).
 * Lihat docs/05-arsitektur.md §2.
 */
class CreateTicketData
{
    public function __construct(
        public readonly string $title,
        public readonly string $description,
        public readonly int $divisionId,
        public readonly string $senderName,
        public readonly int $categoryId,
        public readonly Priority $priority,
        public readonly ?string $location = null,
        public readonly ?string $deadline = null,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            title: $data['title'],
            description: $data['description'],
            divisionId: (int) $data['division_id'],
            senderName: $data['sender_name'],
            categoryId: (int) $data['category_id'],
            priority: Priority::from($data['priority']),
            location: $data['location'] ?? null,
            deadline: $data['deadline'] ?? null,
        );
    }
}
