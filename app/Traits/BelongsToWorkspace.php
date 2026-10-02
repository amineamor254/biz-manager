<?php

namespace App\Traits;

use App\Models\Workspace;
use Illuminate\Database\Eloquent\Builder;

/**
 * Trait for models that belong to a workspace (tenant).
 * Automatically scopes all queries by the current workspace.
 *
 * Add to any model that has a workspace_id column:
 * use BelongsToWorkspace;
 */
trait BelongsToWorkspace
{
    use ScopedByTenant;

    public static function bootBelongsToWorkspace()
    {
        // Automatically set workspace_id on create
        static::creating(function ($model) {
            if (function_exists('current_workspace_id') && current_workspace_id()) {
                $model->workspace_id = current_workspace_id();
            }
        });
    }

    /**
     * Scope query to current workspace
     */
    public function scopeInWorkspace(Builder $query, $workspaceId = null)
    {
        $workspaceId = $workspaceId ?? current_workspace_id();
        return $query->where('workspace_id', $workspaceId);
    }

    /**
     * Get workspace relationship
     */
    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }
}
