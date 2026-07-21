<?php

namespace App\Models;

use App\Enums\Role;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /** @var list<string> */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'division_id',
        'phone',
        'job_title',
        'avatar_path',
        'is_active',
    ];

    /** @var list<string> */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => Role::class,
            'is_active' => 'boolean',
        ];
    }

    // ---------- Relasi ----------

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    /** Tiket yang dibuat user ini (sebagai client). */
    public function createdTickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'created_by');
    }

    /** Tiket yang dikerjakan user ini (sebagai teknisi). */
    public function assignedTickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'assigned_to');
    }

    // ---------- Helper RBAC ----------

    public function isAdmin(): bool
    {
        return $this->role === Role::Admin;
    }

    public function isItSupport(): bool
    {
        return $this->role === Role::ItSupport;
    }

    public function isClient(): bool
    {
        return $this->role === Role::Client;
    }
}
