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
                'message' => 'Successfully logged out. Token invalidated.'
            ]);
        }

        return response()->json([
            'message' => 'No active session found.'
        ], 401);
    }
}