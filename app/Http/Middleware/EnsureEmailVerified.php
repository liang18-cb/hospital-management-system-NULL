<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || !$request->user()->email_verified_at) {
            return response()->json([
                'status' => 'error',
                'message' => 'Alamat email Anda belum diverifikasi.',
                'data' => null,
                'meta' => null,
                'errors' => null
            ], 403);
        }

        return $next($request);
    }
}