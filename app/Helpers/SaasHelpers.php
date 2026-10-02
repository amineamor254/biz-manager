<?php

/**
 * Helper Functions for SaaS
 * Add to app/Helpers/SaasHelpers.php or bootstrap/helpers.php
 */

if (!function_exists('current_workspace_id')) {
    /**
     * Get current workspace ID from app context
     */
    function current_workspace_id(): ?int
    {
        if (app()->bound('workspace_id')) {
            return app('workspace_id');
        }

        return auth()->user()?->getCurrentWorkspace()?->id;
    }
}

if (!function_exists('current_workspace')) {
    /**
     * Get current workspace model
     */
    function current_workspace()
    {
        $user = auth()->user();
        if (!$user) {
            return null;
        }

        return $user->getCurrentWorkspace();
    }
}

if (!function_exists('current_user_workspace_role')) {
    /**
     * Get current user's role in workspace
     */
    function current_user_workspace_role(): ?string
    {
        $user = auth()->user();
        $workspace = current_workspace();

        if (!$user || !$workspace) {
            return null;
        }

        return $user->getRoleInWorkspace($workspace);
    }
}

if (!function_exists('can_access_workspace')) {
    /**
     * Check if user can access workspace
     */
    function can_access_workspace($userId, $workspaceId): bool
    {
        return \App\Models\WorkspaceUser::where('user_id', $userId)
            ->where('workspace_id', $workspaceId)
            ->exists();
    }
}

if (!function_exists('subscription_has_feature')) {
    /**
     * Check if workspace subscription has feature
     */
    function subscription_has_feature(string $feature, $workspace = null): bool
    {
        $workspace = $workspace ?? current_workspace();
        
        if (!$workspace) {
            return false;
        }

        $subscription = $workspace->subscription;
        
        if (!$subscription || $subscription->status !== 'active') {
            return false;
        }

        return $subscription->plan->hasFeature($feature);
    }
}

if (!function_exists('format_workspace_invoice_number')) {
    /**
     * Format invoice number with workspace prefix
     */
    function format_workspace_invoice_number($invoiceId, $workspace = null): string
    {
        $workspace = $workspace ?? current_workspace();
        $slug = $workspace->slug ?? 'DEF';

        return sprintf("INV-%s-%05d", strtoupper($slug), $invoiceId);
    }
}
