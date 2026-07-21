<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware RBAC: membatasi akses route berdasarkan role user (FR-2).
 * Pemakaian: ->middleware('role:admin') atau 'role:admin,it_support'.
 * Lihat docs/05-arsitektur.md §7.
 */
class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if ($user === null) {
            abort(401, 'Tidak terautentikasi.');
        }

        if (! $user->is_active) {
            abort(403, 'Akun Anda tidak aktif.');
        }

        if (! in_array($user->role->value, $roles, true)) {
            abort(403, 'Anda tidak memiliki akses ke sumber daya ini.');
        }

        return $next($request);
    }
}
