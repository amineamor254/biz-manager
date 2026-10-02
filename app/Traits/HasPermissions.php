<?php

namespace App\Traits;

use Closure;

/**
 * Trait for permission/authorization checks
 */
trait HasPermissions
{
    /**
     * Check if user can perform action on workspace
     */
    public function can(string $action, $model): bool
    {
        // Will be extended with policy logic
        return true;
    }

    /**
     * Check if user has specific role in workspace
     */
    public function hasRole(string $role, $workspace = null): bool
    {
        $workspace = $workspace ?? $this->getCurrentWorkspace();
        
        if (!$workspace) {
            return false;
        }

        return $this->getRoleInWorkspace($workspace) === $role;
    }

    /**
     * Verify user can act on resource in their workspace
     */
    public function authorizeWorkspaceResource($resource): bool
    {
        $workspace = $this->getCurrentWorkspace();
        
        if (!$workspace) {
            return false;
        }

        // Check if resource belongs to this workspace
        return $resource->workspace_id === $workspace->id;
    }
}
