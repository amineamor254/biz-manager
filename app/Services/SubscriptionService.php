<?php

namespace App\Services;

use App\Models\Workspace;
use App\Models\Subscription;
use App\Models\Plan;

/**
 * SubscriptionService - Handles all billing/subscription operations
 */
class SubscriptionService
{
    /**
     * Upgrade workspace subscription
     */
    public function upgradePlan(Workspace $workspace, string $planSlug): Subscription
    {
        $plan = Plan::where('slug', $planSlug)->firstOrFail();
        
        $subscription = $workspace->subscription;
        $subscription->update([
            'plan_id' => $plan->id,
            'status' => 'active',
        ]);

        // Log the change
        activity()
            ->causedBy(auth()->user())
            ->on($workspace)
            ->log("Upgraded to {$plan->name} plan");

        return $subscription;
    }

    /**
     * Downgrade workspace subscription
     */
    public function downgradePlan(Workspace $workspace, string $planSlug): Subscription
    {
        return $this->upgradePlan($workspace, $planSlug);
    }

    /**
     * Cancel subscription
     */
    public function cancelSubscription(Workspace $workspace): Subscription
    {
        $subscription = $workspace->subscription;
        $subscription->update([
            'status' => 'cancelled',
            'canceled_at' => now(),
        ]);

        return $subscription;
    }

    /**
     * Check if workspace can use AI features
     */
    public function canUseAiFeatures(Workspace $workspace): bool
    {
        $subscription = $workspace->subscription;
        
        if (!$subscription || $subscription->status !== 'active') {
            return false;
        }

        return $subscription->plan->hasFeature('ai_features');
    }

    /**
     * Check if workspace has API access
     */
    public function hasApiAccess(Workspace $workspace): bool
    {
        $subscription = $workspace->subscription;
        
        if (!$subscription || $subscription->status !== 'active') {
            return false;
        }

        return $subscription->plan->hasFeature('api_access');
    }

    /**
     * Get available workspaces count
     */
    public function getAvailableWorkspacesCount($user): int
    {
        $subscription = $user->getCurrentWorkspace()?->subscription;
        
        if (!$subscription) {
            return 1; // Default free tier
        }

        $max = $subscription->plan->getFeature('max_workspaces');
        return $max ?? 999; // Treat null as unlimited
    }

    /**
     * Get max clients limit
     */
    public function getMaxClientsLimit(Workspace $workspace): ?int
    {
        $subscription = $workspace->subscription;
        
        if (!$subscription) {
            return 10; // Default free tier
        }

        return $subscription->plan->getFeature('max_clients');
    }
}
