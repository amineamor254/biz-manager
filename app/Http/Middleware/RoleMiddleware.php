<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * RoleMiddleware
 * 
 * Checks user role within workspace
 */
class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = auth()->user();
        $workspace = $user->getCurrentWorkspace();

        if (!$workspace) {
            abort(403, 'No active workspace');
        }

        $userRole = $user->getRoleInWorkspace($workspace);

        if (!in_array($userRole, $roles)) {
            abort(403, 'Insufficient permissions for this workspace');
        }

        return $next($request);
    }
}
