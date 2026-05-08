<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Usage:  ->middleware('role:admin')
     *         ->middleware('role:admin,approver_level_1,approver_level_2')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        $userRole = auth()->user()->role?->name;

        foreach ($roles as $role) {
            if ($userRole === $role) {
                return $next($request);
            }
        }

        // Authenticated but wrong role — send back to their landing page
        return match ($userRole) {
            'approver_level_1', 'approver_level_2' => redirect()->route('approval-system'),
            default => abort(403, 'Anda tidak memiliki akses ke halaman ini.'),
        };
    }
}
