<?php

namespace App\Enums;

/**
 * Peran pengguna untuk RBAC. Disimpan sebagai string di kolom users.role.
 * Lihat docs/02-analisis-kebutuhan.md (FR-2) & docs/05-arsitektur.md.
 */
enum Role: string
{
    case Admin = 'admin';
    case ItSupport = 'it_support';
    case Client = 'client';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrator',
            self::ItSupport => 'IT Support',
            self::Client => 'Client',
        };
    }
}
