<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

/**
 * Base tenant scoping trait
 * Enforces workspace isolation on all queries
 */
trait ScopedByTenant
{
    public static function bootScopedByTenant()
    {
        static::addGlobalScope('workspace', function (Builder $query) {
            if (!auth()->check()) {
                return;
            }

            $workspaceId = current_workspace_id();
            $table = $query->getModel()->getTable();

            if (!$workspaceId) {
                $query->whereRaw('1 = 0');
                return;
            }

            $query->where($table . '.workspace_id', $workspaceId);
        });
    }
}