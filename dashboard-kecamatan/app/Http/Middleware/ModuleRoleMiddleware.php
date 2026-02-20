<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Module Role Middleware
 * 
 * Provides module-specific access control for isolated modules.
 * Each module has its own admin role, and Super Admin has access to all modules.
 * 
 * Usage: Route::middleware(['module.role:trantibum'])->group(...)
 * 
 * Module Role Mapping:
 * - trantibum -> trantibum_admin, Super Admin
 * - umkm -> umkm_admin, Super Admin
 * - loker -> loker_admin, Super Admin
 * - pelayanan -> pelayanan_admin, Super Admin
 */
class ModuleRoleMiddleware
{
    /**
     * Module to role mapping
     */
    protected array $moduleRoles = [
        'trantibum' => ['trantibum_admin', 'Super Admin', 'Operator Kecamatan'],
        'umkm' => ['umkm_admin', 'Super Admin', 'Operator Kecamatan'],
        'loker' => ['loker_admin', 'Super Admin', 'Operator Kecamatan'],
        'pelayanan' => ['pelayanan_admin', 'Super Admin', 'Operator Kecamatan'],
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $module  The module identifier (trantibum, umkm, loker, pelayanan)
     */
    public function handle(Request $request, Closure $next, string $module): Response
    {
        if (!$request->user()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('login');
        }

        $user = $request->user();
        $userRole = $user->role->nama_role ?? null;

        // Get allowed roles for this module
        $allowedRoles = $this->moduleRoles[$module] ?? [];

        // If no mapping exists, default to Super Admin and Operator Kecamatan only
        if (empty($allowedRoles)) {
            $allowedRoles = ['Super Admin', 'Operator Kecamatan'];
        }

        // Check if user has an allowed role
        if (!$userRole || !in_array($userRole, $allowedRoles)) {
            \Log::channel('daily')->warning('Module access denied', [
                'user_id' => $user->id,
                'user_role' => $userRole,
                'module' => $module,
                'allowed_roles' => $allowedRoles,
                'ip' => $request->ip(),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Akses ditolak. Anda tidak memiliki izin untuk modul ini.',
                    'module' => $module,
                ], 403);
            }

            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengakses modul ' . ucfirst($module) . '.');
        }

        // Log successful module access (optional, for audit trail)
        \Log::channel('daily')->info('Module access granted', [
            'user_id' => $user->id,
            'user_role' => $userRole,
            'module' => $module,
            'route' => $request->route()->getName(),
        ]);

        return $next($request);
    }
}
