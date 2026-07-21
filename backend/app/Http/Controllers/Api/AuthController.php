<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * Autentikasi berbasis token Sanctum untuk SPA (docs/05-arsitektur.md §6).
 * Controller tipis: hanya orkestrasi; validasi di Form Request.
 */
class AuthController extends Controller
{
    public function __construct(private readonly ActivityLogger $activity) {}

    /** POST /api/auth/login — kembalikan token + data user. */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        // Pesan seragam untuk email/password salah (hindari user enumeration).
        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau password salah.'],
            ]);
        }

        if (! $user->is_active) {
            throw ValidationException::withMessages([
                'email' => ['Akun Anda tidak aktif. Hubungi administrator.'],
            ]);
        }

        $token = $user->createToken($request->input('device_name', 'spa'))->plainTextToken;

        $this->activity->log($user, 'login');

        return response()->json([
            'message' => 'Berhasil masuk.',
            'data' => [
                'token' => $token,
                'user' => new UserResource($user->load('division')),
            ],
        ]);
    }

    /** GET /api/auth/me — profil user yang sedang login. */
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'data' => new UserResource($request->user()->load('division')),
        ]);
    }

    /** POST /api/auth/logout — cabut token yang sedang dipakai. */
    public function logout(Request $request): JsonResponse
    {
        $this->activity->log($request->user(), 'logout');
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Berhasil keluar.']);
    }
}
