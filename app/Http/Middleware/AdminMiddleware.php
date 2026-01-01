<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah user yang login memiliki role 'admin'
        if ($request->user() && $request->user()->role !== 'admin') {
            return response()->json([
                'message' => 'Akses ditolak. Anda bukan Admin!'
            ], 403); // 403 artinya Forbidden (Terlarang)
        }

        return $next($request);
    }
}
