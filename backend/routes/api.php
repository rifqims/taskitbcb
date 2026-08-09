<?php

use App\Http\Controllers\Api\AdminLogController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ReferenceController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\TicketMessageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — Gawe-Qi
|--------------------------------------------------------------------------
| Autentikasi token Sanctum + guard RBAC via middleware 'role'.
| Modul tiket, chat, laporan menyusul pada langkah berikutnya.
*/

// Publik — login dibatasi rate limit untuk cegah brute force (NFR-1).
Route::post('/auth/login', [AuthController::class, 'login'])
    ->middleware('throttle:6,1');

// Terproteksi (butuh token valid).
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // Data referensi untuk form.
    Route::get('/references', [ReferenceController::class, 'index']);

    // ---- Modul Tiket ----
    Route::get('/tickets', [TicketController::class, 'index']);
    Route::post('/tickets', [TicketController::class, 'store']);            // client
    Route::get('/tickets/{ticket}', [TicketController::class, 'show']);
    Route::post('/tickets/{ticket}/assign', [TicketController::class, 'assign']);          // IT ambil
    Route::patch('/tickets/{ticket}/progress', [TicketController::class, 'updateProgress']);
    Route::patch('/tickets/{ticket}/status', [TicketController::class, 'changeStatus']);
    Route::post('/tickets/{ticket}/resolve', [TicketController::class, 'resolve']);

    // ---- Chat pada tiket ----
    Route::get('/tickets/{ticket}/messages', [TicketMessageController::class, 'index']);
    Route::post('/tickets/{ticket}/messages', [TicketMessageController::class, 'store']);

    // ---- Notifikasi ----
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead']);
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead']);

    // ---- Laporan & Analytics (admin + IT) ----
    Route::middleware('role:admin,it_support')->group(function () {
        Route::get('/reports/summary', [ReportController::class, 'summary']);
        Route::get('/reports/analytics', [ReportController::class, 'analytics']);
        Route::get('/reports/export', [ReportController::class, 'exportCsv']);
    });

    // ---- Log (admin only) — FR-25/26 ----
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/activity-logs', [AdminLogController::class, 'activity']);
        Route::get('/admin/audit-logs', [AdminLogController::class, 'audit']);
    });

    // Contoh route ber-guard role (bukti RBAC berjalan).
    Route::get('/admin/ping', fn (Request $r) => response()->json(['ok' => true, 'area' => 'admin']))
        ->middleware('role:admin');
    Route::get('/it/ping', fn (Request $r) => response()->json(['ok' => true, 'area' => 'it']))
        ->middleware('role:admin,it_support');
});
