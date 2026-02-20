<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!$request->user()) {
            return redirect('login');
        }

        // Ambil nama role dari relasi (User belongsTo Role)
        $userRole = $request->user()->role->nama_role ?? null;

        if (!$userRole || !in_array($userRole, $roles)) {
            // Jika akses ditolak, kirim 403 atau redirect dengan pesan error
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized domain access.'], 403);
            }

            abort(403, 'Anda tidak memiliki hak akses untuk masuk ke domain ini.');
        }

        return $next($request);
    }
}
