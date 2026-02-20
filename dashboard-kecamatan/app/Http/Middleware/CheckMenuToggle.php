<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Menu;
use Symfony\Component\HttpFoundation\Response;

class CheckMenuToggle
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $kodeMenu): Response
    {
        $menu = Menu::where('kode_menu', $kodeMenu)->first();

        if ($menu && !$menu->is_active) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Fitur ' . $menu->nama_menu . ' sedang dinonaktifkan oleh Kecamatan.'
                ], 403);
            }

            return redirect()->route('dashboard')->with('error', 'Fitur ' . $menu->nama_menu . ' sedang dinonaktifkan oleh Kecamatan.');
        }

        return $next($request);
    }
}
