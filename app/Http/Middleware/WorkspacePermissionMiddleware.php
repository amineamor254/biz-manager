<?php

namespace App\Http\Middleware;

use App\Services\WorkspaceRoleService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WorkspacePermissionMiddleware
{
    public function handle(Request $request, Closure $next, string $module): Response
    {
        $workspaceId = current_workspace_id();
        abort_unless($workspaceId, 403);

        $action = match ($request->route()->getActionMethod()) {
            'index', 'show' => 'view',
            'create', 'store' => 'create',
            'edit', 'update' => 'edit',
            'destroy' => 'delete',
            default => null,
        };
        abort_unless($action, 403);

        app(WorkspaceRoleService::class)->authorizePermission(
            $request->user(),
            (int) $workspaceId,
            $module . '.' . $action,
        );

        return $next($request);
    }
}