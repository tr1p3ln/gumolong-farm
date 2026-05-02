<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * Restrict access to users whose role is in the allowed list.
     *
     * Usage in routes:  ->middleware('role:super_admin,admin')
     *
     * - pengurus_kandang hitting a restricted route → redirect to mobile home
     * - any other unauthorised role → HTTP 403
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = auth()->user();

        if ($user && in_array($user->role, $roles)) {
            return $next($request);
        }

        // Pengurus kandang should never see web pages → send to mobile home
        if ($user?->role === 'pengurus_kandang') {
            return redirect()->route('tugas-harian.mobile');
        }

        abort(403, 'Anda tidak memiliki akses ke halaman ini.');
    }
}
