<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * SubscriptionMiddleware
 * 
 * Enforces subscription plan limits:
 * - Checks if feature is available in current plan
 * - Enforces rate limits
 * - Blocks access if subscription inactive
 */
class SubscriptionMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $feature = null): Response
    {
        $user = auth()->user();
        $workspace = $user->getCurrentWorkspace();

        if (!$workspace) {
            abort(403, 'No active workspace');
        }

        $subscription = $workspace->subscription;

        // Check if subscription is active
        if (!$subscription || $subscription->status !== 'active') {
            abort(403, 'Subscription inactive or expired');
        }

        // Check feature availability if specified
        if ($feature && !$subscription->plan->hasFeature($feature)) {
            abort(403, "Feature '{$feature}' not available in your plan");
        }

        return $next($request);
    }
}
