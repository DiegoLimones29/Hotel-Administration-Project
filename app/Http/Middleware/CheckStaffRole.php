<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckStaffRole
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || ! in_array($request->user()->role, ['admin', 'recep'])) {
            return response()->json([
                'message' => 'Acceso denegado. Solo personal del hotel.'
            ], 403);
        }

        return $next($request);
    }
}