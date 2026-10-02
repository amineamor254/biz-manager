<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * WorkspaceMiddleware
 * 
 * Handles multi-tenancy:
 * 1. Extracts workspace from URL or session
 * 2. Verifies user access
 * 3. Sets application context
 * 4. Prevents cross-workspace data leakage
 */
class WorkspaceMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (!$user) {
            return redirect('/login');
        }

        $routeWorkspace = $request->route('workspace');
        $workspaceId = is_object($routeWorkspace)
            ? $routeWorkspace->getKey()
            : ($routeWorkspace ?: $user->current_workspace_id);

        if (!$workspaceId) {
            $workspace = $user->workspaces()
                ->wherePivotNotNull('accepted_at')
                ->first();
            
            if (!$workspace) {
                return redirect('/onboarding');
            }

            $workspaceId = $workspace->id;
        }

        // Verify user can access this workspace
        $hasAccess = $user->workspaces()
            ->whereKey($workspaceId)
            ->wherePivotNotNull('accepted_at')
            ->exists();

        if (!$hasAccess) {
            abort(403, 'Unauthorized workspace access');
        }

        // Set global workspace context
        app()->instance('workspace_id', $workspaceId);

        // Update user's current workspace
        if ($user->current_workspace_id !== $workspaceId) {
            $user->update(['current_workspace_id' => $workspaceId]);
        }

        return $next($request);
    }
}
