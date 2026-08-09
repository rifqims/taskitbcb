<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Division;
use Illuminate\Http\JsonResponse;

/**
 * Data referensi untuk form (divisi & kategori aktif). Dipakai form Buat Tiket.
 */
class ReferenceController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => [
                'divisions' => Division::where('is_active', true)->orderBy('name')->get(['id', 'name']),
                'categories' => Category::where('is_active', true)->orderBy('name')->get(['id', 'name', 'icon']),
            ],
        ]);
    }
}
