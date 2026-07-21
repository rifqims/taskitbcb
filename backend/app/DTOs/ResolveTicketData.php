<?php

namespace App\DTOs;

use App\Enums\ResolutionOutcome;

/**
 * Data penyelesaian tiket (FR-13). Untuk outcome=rejected, field reason/berita_acara/
 * recommendation wajib (divalidasi di ResolveTicketRequest).
 */
class ResolveTicketData
{
    public function __construct(
        public readonly ResolutionOutcome $outcome,
        public readonly ?string $reason = null,
        public readonly ?string $beritaAcara = null,
        public readonly ?string $recommendation = null,
        public readonly ?string $notes = null,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            outcome: ResolutionOutcome::from($data['outcome']),
            reason: $data['reason'] ?? null,
            beritaAcara: $data['berita_acara'] ?? null,
            recommendation: $data['recommendation'] ?? null,
            notes: $data['notes'] ?? null,
        );
    }
}
