<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): mixed
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $userRole = auth()->user()->role;

        if (in_array($userRole, $roles)) {
            return $next($request);
        }

        // Redirect sesuai role
        return match($userRole) {
            'superadmin', 'admin', 'tu', 'kepala_sekolah' => abort(403, 'Akses tidak diizinkan.'),
            'wali_kelas', 'guru_mapel' => redirect()->route('teacher.sessions.index')
                ->with('error', 'Anda tidak punya akses ke halaman itu.'),
            default => redirect()->route('scan.index'),
        };
    }
}
