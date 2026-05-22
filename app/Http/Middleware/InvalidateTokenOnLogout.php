<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InvalidateTokenOnLogout
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()) {
            $request->user()->currentAccessToken()->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Sesi berhasil diakhiri, token telah dihapus.',
                'data' => null,
                'meta' => null,
                'errors' => null
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Sesi aktif tidak ditemukan.',
            'data' => null,
            'meta' => null,
            'errors' => null
        ], 401);
    }
}