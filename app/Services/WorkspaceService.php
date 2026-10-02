<?php

namespace App\Services;

use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceUser;
use Illuminate\Support\Str;

/**
 * WorkspaceService - Handles all workspace-related operations
 * Multi-tenancy core logic
 */
class WorkspaceService
{
    /**
     * Create a new workspace
     */
    public function createWorkspace(User $user, array $data): Workspace
    {
        $workspace = Workspace::create([
            'user_id' => $user->id,
            'name' => $data['name'],
            'slug' => $this->generateUniqueSlug($data['name']),
            'timezone' => $data['timezone'] ?? 'UTC',
            'currency' => $data['currency'] ?? 'USD',
            'is_active' => true,
        ]);

        // Add user as owner
        WorkspaceUser::create([
            'workspace_id' => $workspace->id,
            'user_id' => $user->id,
            'role' => 'owner',
            'accepted_at' => now(),
        ]);

        return $workspace;
    }

    /**
     * Update workspace
     */
    public function updateWorkspace(Workspace $workspace, array $data): Workspace
    {
        $workspace->update($data);
        return $workspace;
    }

    /**
     * Add user to workspace
     */
    public function inviteUserToWorkspace(Workspace $workspace, string $email, string $role): WorkspaceUser
    {
        // Find or create user
        $user = User::where('email', $email)->firstOrCreate(
            ['email' => $email],
            ['name' => explode('@', $email)[0], 'role' => 'user']
        );

        // Create membership
        return WorkspaceUser::firstOrCreate(
            ['workspace_id' => $workspace->id, 'user_id' => $user->id],
            ['role' => $role, 'invited_at' => now()]
        );
    }

    /**
     * Remove user from workspace
     */
    public function removeUserFromWorkspace(Workspace $workspace, User $user): bool
    {
        return WorkspaceUser::where('workspace_id', $workspace->id)
            ->where('user_id', $user->id)
            ->delete() > 0;
    }

    /**
     * Update user role in workspace
     */
    public function updateUserRole(Workspace $workspace, User $user, string $role): WorkspaceUser
    {
        return tap(
            WorkspaceUser::where('workspace_id', $workspace->id)
                ->where('user_id', $user->id)
                ->first(),
            fn($member) => $member->update(['role' => $role])
        );
    }

    /**
     * Check if user can access workspace
     */
    public function userCanAccessWorkspace(User $user, Workspace $workspace): bool
    {
        return $user->workspaces()
            ->where('workspaces.id', $workspace->id)
            ->exists();
    }

    /**
     * Generate unique workspace slug
     */
    private function generateUniqueSlug(string $name): string
    {
        $slug = Str::slug($name);
        $original = $slug;
        $counter = 1;

        while (Workspace::where('slug', $slug)->exists()) {
            $slug = "{$original}-{$counter}";
            $counter++;
        }

        return $slug;
    }

    /**
     * Check workspace plan limits
     */
    public function canAddClientsToWorkspace(Workspace $workspace): bool
    {
        $subscription = $workspace->subscription;
        if (!$subscription) {
            return false;
        }

        $maxClients = $subscription->plan->getFeature('max_clients');
        
        // unlimited if null
        if ($maxClients === null) {
            return true;
        }

        $currentCount = $workspace->clients()->count();
        return $currentCount < $maxClients;
    }
}
