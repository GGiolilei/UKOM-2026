<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Cek apakah user terautentikasi dan role-nya sesuai dengan parameter yang diizinkan
        if (! $request->user() || ! in_array($request->user()->role, $roles)) {
            return response()->json([
                'message' => 'Akses ditolak. Anda tidak memiliki hak akses.'
            ], 403);
        }

        return $next($request);
    }
}