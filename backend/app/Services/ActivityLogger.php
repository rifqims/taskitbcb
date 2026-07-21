<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

/**
 * Mencatat Activity Log global (FR-25): aksi + user + IP + browser (user agent).
 * IP & user agent diambil dari request aktif.
 */
class ActivityLogger
{
    /**
     * @param  array<string, mixed>  $context
     */
    public function log(?User $user, string $action, ?Model $subject = null, array $context = []): ActivityLog
    {
        return ActivityLog::create([
            'user_id' => $user?->id,
            'action' => $action,
            'subject_type' => $subject ? $subject::class : null,
            'subject_id' => $subject?->getKey(),
            'ip_address' => Request::ip(),
            'user_agent' => (string) Request::userAgent(),
            'context' => $context,
        ]);
    }
}
