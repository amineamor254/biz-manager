<?php

namespace App\Http\Controllers;

use App\Models\WorkspaceRole;
use App\Models\WorkspaceUser;
use App\Services\WorkspaceRoleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class RolesPermissionsController extends Controller
{
    public function index(WorkspaceRoleService $roles)
    {
        $workspaceId = $this->workspaceId();
        $roles->ensureBuiltInRoles($workspaceId);
        $user = auth()->user();
        abort_unless(
            $roles->hasPermission($user, $workspaceId, 'roles.view') || $roles->hasPermission($user, $workspaceId, 'roles.manage'),
            403,
        );

        $roleList = WorkspaceRole::query()->where('workspace_id', $workspaceId)->orderBy('is_system', 'desc')->orderBy('name')->get();
        $memberList = WorkspaceUser::query()->with('user')->where('workspace_id', $workspaceId)->orderBy('id')->get();
        $memberCounts = $memberList->countBy('role');
        $permissionGroups = $roles->permissionGroups();
        $permissionLabels = [];
        foreach ($permissionGroups as $group => $permissions) {
            foreach ($permissions as $key => $label) {
                $permissionLabels[$key] = $group . ' ' . strtolower($label);
            }
        }
        $canManageRoles = $roles->hasPermission($user, $workspaceId, 'roles.manage');
        $canManageUsers = $roles->hasPermission($user, $workspaceId, 'users.manage');
        $isOwner = $roles->isOwner($user, $workspaceId);
        $assignableRoles = $roleList->filter(function (WorkspaceRole $role) use ($roles, $user, $workspaceId): bool {
            try {
                $roles->authorizeRoleAssignment($user, $workspaceId, $role);
                return true;
            } catch (\Illuminate\Auth\Access\AuthorizationException) {
                return false;
            }
        });

        return view('roles-permissions.index', compact(
            'roleList', 'memberList', 'memberCounts', 'permissionGroups', 'permissionLabels',
            'canManageRoles', 'canManageUsers', 'isOwner', 'assignableRoles',
        ));
    }

    public function storeRole(Request $request, WorkspaceRoleService $roles)
    {
        $workspaceId = $this->workspaceId();
        $actor = auth()->user();
        $roles->authorizePermission($actor, $workspaceId, 'roles.manage');
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:80', Rule::unique('workspace_roles', 'name')->where('workspace_id', $workspaceId)],
            'permissions' => ['sometimes', 'array'],
            'permissions.*' => ['string', Rule::in($roles->allPermissions())],
            'workspace_id' => ['prohibited'],
        ]);
        $permissions = array_values(array_unique($validated['permissions'] ?? []));
        $roles->authorizeGrant($actor, $workspaceId, $permissions);
        $roles->createRole($workspaceId, $validated['name'], $permissions);

        return back()->with('success', 'Role created successfully.');
    }

    public function updateRole(Request $request, int $roleId, WorkspaceRoleService $roles)
    {
        $workspaceId = $this->workspaceId();
        $role = WorkspaceRole::query()->where('workspace_id', $workspaceId)->whereKey($roleId)->firstOrFail();
        $actor = auth()->user();
        $roles->authorizeRoleEdit($actor, $workspaceId, $role);
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:80', Rule::unique('workspace_roles', 'name')->where('workspace_id', $workspaceId)->ignore($role->id)],
            'permissions' => ['sometimes', 'array'],
            'permissions.*' => ['string', Rule::in($roles->allPermissions())],
            'workspace_id' => ['prohibited'],
        ]);
        $permissions = array_values(array_unique($validated['permissions'] ?? []));
        $roles->authorizeGrant($actor, $workspaceId, $permissions);
        $role->update(['name' => $validated['name'], 'permissions' => $permissions]);

        return back()->with('success', 'Role updated successfully.');
    }

    public function destroyRole(int $roleId, WorkspaceRoleService $roles)
    {
        $workspaceId = $this->workspaceId();
        $role = WorkspaceRole::query()->where('workspace_id', $workspaceId)->whereKey($roleId)->firstOrFail();
        $roles->authorizeRoleEdit(auth()->user(), $workspaceId, $role);

        if ($role->is_system) {
            throw ValidationException::withMessages(['role' => 'Built-in roles cannot be deleted.']);
        }

        if (WorkspaceUser::query()->where('workspace_id', $workspaceId)->where('role', $role->slug)->exists()) {
            throw ValidationException::withMessages(['role' => 'Reassign this role’s members before deleting it.']);
        }

        $role->delete();

        return back()->with('success', 'Role deleted successfully.');
    }

    public function updateMemberRole(Request $request, int $membershipId, WorkspaceRoleService $roles)
    {
        $workspaceId = $this->workspaceId();
        $membership = WorkspaceUser::query()->where('workspace_id', $workspaceId)->whereKey($membershipId)->firstOrFail();
        $actor = auth()->user();
        $roles->authorizePermission($actor, $workspaceId, 'users.manage');
        $validated = $request->validate([
            'role_id' => ['required', 'integer', Rule::exists('workspace_roles', 'id')->where('workspace_id', $workspaceId)],
            'workspace_id' => ['prohibited'],
        ]);
        $role = WorkspaceRole::query()->where('workspace_id', $workspaceId)->whereKey($validated['role_id'])->firstOrFail();
        $roles->authorizeRoleAssignment($actor, $workspaceId, $role);

        if ((int) $membership->user_id === (int) $actor->id && $roles->isOwner($actor, $workspaceId)) {
            throw ValidationException::withMessages(['role_id' => 'The workspace owner cannot change their own role.']);
        }

        DB::transaction(fn () => $membership->update(['role' => $role->slug]));

        return back()->with('success', 'Workspace member role updated.');
    }

    private function workspaceId(): int
    {
        $workspaceId = current_workspace_id();
        abort_unless($workspaceId, 403);

        return (int) $workspaceId;
    }
}