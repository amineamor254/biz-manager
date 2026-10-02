<?php

namespace App\Services;

use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceRole;
use App\Models\WorkspaceUser;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Str;

class WorkspaceRoleService
{
    public function permissionGroups(): array
    {
        return [
            'Dashboard' => ['dashboard.view' => 'View'],
            'Clients' => ['clients.view' => 'View', 'clients.create' => 'Create', 'clients.edit' => 'Edit', 'clients.delete' => 'Delete'],
            'Products' => ['products.view' => 'View', 'products.create' => 'Create', 'products.edit' => 'Edit', 'products.delete' => 'Delete'],
            'Invoices' => ['invoices.view' => 'View', 'invoices.create' => 'Create', 'invoices.edit' => 'Edit', 'invoices.delete' => 'Delete'],
            'Orders' => ['orders.view' => 'View', 'orders.create' => 'Create', 'orders.edit' => 'Edit', 'orders.delete' => 'Delete'],
            'Expenses' => ['expenses.view' => 'View', 'expenses.create' => 'Create', 'expenses.edit' => 'Edit', 'expenses.delete' => 'Delete'],
            'Reports' => ['reports.view' => 'View'],
            'Roles & Permissions' => ['roles.view' => 'View', 'roles.manage' => 'Manage roles', 'users.manage' => 'Manage workspace users'],
        ];
    }

    public function allPermissions(): array
    {
        return collect($this->permissionGroups())->flatMap(fn (array $permissions) => array_keys($permissions))->all();
    }

    public function ensureBuiltInRoles(int $workspaceId): void
    {
        $all = $this->allPermissions();

        foreach ([
            ['name' => 'Owner', 'slug' => 'owner', 'permissions' => $all],
            ['name' => 'Admin', 'slug' => 'admin', 'permissions' => $all],
            ['name' => 'Staff', 'slug' => 'staff', 'permissions' => []],
        ] as $role) {
            WorkspaceRole::query()->firstOrCreate(
                ['workspace_id' => $workspaceId, 'slug' => $role['slug']],
                ['name' => $role['name'], 'permissions' => $role['permissions'], 'is_system' => true],
            );
        }
    }

    public function membership(User $user, int $workspaceId): ?WorkspaceUser
    {
        return WorkspaceUser::query()
            ->where('workspace_id', $workspaceId)
            ->where('user_id', $user->id)
            ->whereNotNull('accepted_at')
            ->first();
    }

    public function isOwner(User $user, int $workspaceId): bool
    {
        $membership = $this->membership($user, $workspaceId);
        $workspaceOwnerId = Workspace::query()->whereKey($workspaceId)->value('user_id');

        return $membership?->role === 'owner' || (int) $workspaceOwnerId === (int) $user->id;
    }

    public function permissionsFor(User $user, int $workspaceId): array
    {
        $this->ensureBuiltInRoles($workspaceId);
        $membership = $this->membership($user, $workspaceId);

        if (!$membership) {
            return [];
        }

        if ($this->isOwner($user, $workspaceId)) {
            return $this->allPermissions();
        }

        return WorkspaceRole::query()
            ->where('workspace_id', $workspaceId)
            ->where('slug', $membership->role)
            ->first()?->permissions ?? [];
    }

    public function hasPermission(User $user, int $workspaceId, string $permission): bool
    {
        return in_array($permission, $this->permissionsFor($user, $workspaceId), true);
    }

    public function authorizePermission(User $user, int $workspaceId, string $permission): void
    {
        if (!$this->hasPermission($user, $workspaceId, $permission)) {
            throw new AuthorizationException('You are not authorized to manage workspace roles or members.');
        }
    }

    public function authorizeGrant(User $user, int $workspaceId, array $permissions): void
    {
        if (array_diff($permissions, $this->permissionsFor($user, $workspaceId))) {
            throw new AuthorizationException('You cannot grant permissions you do not have.');
        }
    }

    public function createRole(int $workspaceId, string $name, array $permissions): WorkspaceRole
    {
        $slug = Str::slug($name);
        $baseSlug = $slug;
        $suffix = 2;

        while (WorkspaceRole::query()->where('workspace_id', $workspaceId)->where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $suffix++;
        }

        return WorkspaceRole::create([
            'workspace_id' => $workspaceId,
            'name' => $name,
            'slug' => $slug,
            'permissions' => $permissions,
            'is_system' => false,
        ]);
    }

    public function authorizeRoleEdit(User $user, int $workspaceId, WorkspaceRole $role): void
    {
        $this->authorizePermission($user, $workspaceId, 'roles.manage');

        if ($role->is_system && !$this->isOwner($user, $workspaceId)) {
            throw new AuthorizationException('Only the workspace owner can change built-in roles.');
        }
    }

    public function authorizeRoleAssignment(User $user, int $workspaceId, WorkspaceRole $role): void
    {
        $this->authorizePermission($user, $workspaceId, 'users.manage');

        if (in_array($role->slug, ['owner', 'admin'], true) && !$this->isOwner($user, $workspaceId)) {
            throw new AuthorizationException('Only the workspace owner can assign Owner or Admin.');
        }

        $this->authorizeGrant($user, $workspaceId, $role->permissions ?? []);
    }
}