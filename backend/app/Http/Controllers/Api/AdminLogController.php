<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ActivityLogResource;
use App\Http\Resources\AuditLogResource;
use App\Models\ActivityLog;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Akses log untuk Administrator (FR-25/26). Route sudah ber-guard role:admin.
 */
class AdminLogController extends Controller
{
    public function activity(Request $request): AnonymousResourceCollection
    {
        $query = ActivityLog::query()->with('user')->latest();

        if ($action = $request->string('action')->toString()) {
            $query->where('action', $action);
        }
        if ($userId = $request->integer('user_id')) {
            $query->where('user_id', $userId);
        }

        return ActivityLogResource::collection($query->paginate(min($request->integer('per_page', 25), 100)));
    }

    public function audit(Request $request): AnonymousResourceCollection
    {
        $query = AuditLog::query()->with('user')->latest();

        if ($event = $request->string('event')->toString()) {
            $query->where('event', $event);
        }
        if ($id = $request->integer('auditable_id')) {
            $query->where('auditable_id', $id);
        }

        return AuditLogResource::collection($query->paginate(min($request->integer('per_page', 25), 100)));
    }
}
