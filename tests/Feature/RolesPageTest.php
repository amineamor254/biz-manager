<?php

use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceRole;
use App\Models\WorkspaceUser;

function createRolesWorkspaceMember(string $email, string $role = 'owner'): array
{
    $user = User::factory()->create(['email' => $email]);
    $workspace = Workspace::create([
        'user_id' => $user->id,
        'name' => $email . ' Business',
        'slug' => str_replace(['@', '.'], '-', $email) . '-' . $user->id,
    ]);
    $membership = WorkspaceUser::create([
        'workspace_id' => $workspace->id,
        'user_id' => $user->id,
        'role' => $role,
        'accepted_at' => now(),
    ]);
    $user->update(['current_workspace_id' => $workspace->id]);

    return [$user->fresh(), $workspace, $membership];
}

function addRolesWorkspaceMember(Workspace $workspace, string $email, string $role): array
{
    $user = User::factory()->create(['email' => $email]);
    $membership = WorkspaceUser::create([
        'workspace_id' => $workspace->id,
        'user_id' => $user->id,
        'role' => $role,
        'accepted_at' => now(),
    ]);
    $user->update(['current_workspace_id' => $workspace->id]);

    return [$user->fresh(), $membership];
}

test('roles permissions page is displayed to an authorized workspace user', function () {
    [$user] = createRolesWorkspaceMember('roles-page@example.com');

    $response = $this
        ->actingAs($user)
        ->get('/roles-permissions');

    $response->assertOk()
        ->assertSee('Roles & Permissions')
        ->assertSee('Create Role')
        ->assertSee('Workspace members')
        ->assertSee('Owner')
        ->assertSee('Admin')
        ->assertSee('Staff');
});

test('unauthenticated users cannot access roles and permissions', function () {
    $this->get('/roles-permissions')->assertRedirect('/login');
});

test('owner can create and edit workspace roles and permissions', function () {
    [$owner, $workspace] = createRolesWorkspaceMember('roles-owner@example.com');

    $this->actingAs($owner)->post(route('roles-permissions.roles.store'), [
        'name' => 'Order Clerk',
        'permissions' => ['orders.view', 'orders.create'],
    ])->assertRedirect();

    $role = WorkspaceRole::query()->where('workspace_id', $workspace->id)->where('slug', 'order-clerk')->firstOrFail();
    expect($role->permissions)->toBe(['orders.view', 'orders.create']);

    $this->put(route('roles-permissions.roles.update', $role), [
        'name' => 'Order Operator',
        'permissions' => ['orders.view', 'orders.edit'],
    ])->assertRedirect();

    expect($role->fresh()->name)->toBe('Order Operator')
        ->and($role->fresh()->permissions)->toBe(['orders.view', 'orders.edit'])
        ->and($role->fresh()->workspace_id)->toBe($workspace->id);
});

test('role names are unique within a workspace and may be reused in another workspace', function () {
    [$firstOwner, $firstWorkspace] = createRolesWorkspaceMember('roles-name-first@example.com');
    [$secondOwner, $secondWorkspace] = createRolesWorkspaceMember('roles-name-second@example.com');

    $this->actingAs($firstOwner)->post(route('roles-permissions.roles.store'), [
        'name' => 'Analyst',
        'permissions' => [],
    ])->assertRedirect();

    $this->post(route('roles-permissions.roles.store'), [
        'name' => 'Analyst',
        'permissions' => [],
    ])->assertSessionHasErrors('name');

    $this->assertDatabaseCount('workspace_roles', 4);

    $this->actingAs($secondOwner)->post(route('roles-permissions.roles.store'), [
        'name' => 'Analyst',
        'permissions' => [],
    ])->assertRedirect();

    $this->assertDatabaseHas('workspace_roles', [
        'workspace_id' => $firstWorkspace->id,
        'name' => 'Analyst',
    ]);
    $this->assertDatabaseHas('workspace_roles', [
        'workspace_id' => $secondWorkspace->id,
        'name' => 'Analyst',
    ]);
});

test('updating a role while keeping its existing name is allowed', function () {
    [$owner, $workspace] = createRolesWorkspaceMember('roles-name-update@example.com');
    $role = WorkspaceRole::query()->create([
        'workspace_id' => $workspace->id,
        'name' => 'Analyst',
        'slug' => 'analyst',
        'permissions' => [],
        'is_system' => false,
    ]);

    $this->actingAs($owner)->put(route('roles-permissions.roles.update', $role), [
        'name' => 'Analyst',
        'permissions' => [],
    ])->assertRedirect()->assertSessionHasNoErrors();

    expect($role->fresh()->name)->toBe('Analyst');
});

test('staff without administrative permissions cannot view or manage roles', function () {
    [, $workspace] = createRolesWorkspaceMember('roles-staff-owner@example.com');
    [$staff] = addRolesWorkspaceMember($workspace, 'roles-staff@example.com', 'staff');
    WorkspaceRole::query()->create([
        'workspace_id' => $workspace->id,
        'name' => 'Staff',
        'slug' => 'staff',
        'permissions' => ['orders.view'],
        'is_system' => true,
    ]);

    $this->actingAs($staff)->get('/roles-permissions')->assertForbidden();
    $this->post(route('roles-permissions.roles.store'), ['name' => 'Elevated', 'permissions' => ['orders.view']])->assertForbidden();
});

test('role and member management cannot cross workspace boundaries', function () {
    [$owner, $workspace] = createRolesWorkspaceMember('roles-tenant-a@example.com');
    [$otherOwner, $otherWorkspace, $otherMembership] = createRolesWorkspaceMember('roles-tenant-b@example.com');
    $ownMemberUser = User::factory()->create();
    $ownMember = WorkspaceUser::create([
        'workspace_id' => $workspace->id,
        'user_id' => $ownMemberUser->id,
        'role' => 'staff',
        'accepted_at' => now(),
    ]);
    $foreignRole = WorkspaceRole::query()->create([
        'workspace_id' => $otherWorkspace->id,
        'name' => 'Foreign role',
        'slug' => 'foreign-role',
        'permissions' => [],
        'is_system' => false,
    ]);

    $this->actingAs($owner)->get('/roles-permissions')->assertOk()->assertDontSee('Foreign role');
    $this->put(route('roles-permissions.roles.update', $foreignRole), ['name' => 'Changed', 'permissions' => []])->assertNotFound();
    $this->put(route('roles-permissions.members.update', $otherMembership), ['role_id' => $foreignRole->id])->assertNotFound();
    $this->put(route('roles-permissions.members.update', $ownMember), ['role_id' => $foreignRole->id])->assertSessionHasErrors('role_id');
    expect($otherOwner->fresh()->current_workspace_id)->toBe($otherWorkspace->id);
});

test('role assignment is limited to members in the current workspace', function () {
    [$owner, $workspace] = createRolesWorkspaceMember('roles-assign@example.com');
    $memberUser = User::factory()->create();
    $member = WorkspaceUser::create([
        'workspace_id' => $workspace->id,
        'user_id' => $memberUser->id,
        'role' => 'staff',
        'accepted_at' => now(),
    ]);
    $role = WorkspaceRole::query()->create([
        'workspace_id' => $workspace->id,
        'name' => 'Read only orders',
        'slug' => 'read-only-orders',
        'permissions' => ['orders.view'],
        'is_system' => false,
    ]);

    $this->actingAs($owner)->put(route('roles-permissions.members.update', $member), ['role_id' => $role->id])->assertRedirect();
    expect($member->fresh()->role)->toBe('read-only-orders');
});

test('users cannot grant permissions they do not have', function () {
    [, $workspace] = createRolesWorkspaceMember('roles-admin-owner@example.com');
    [$admin] = addRolesWorkspaceMember($workspace, 'roles-admin@example.com', 'admin');
    WorkspaceRole::query()->create([
        'workspace_id' => $workspace->id,
        'name' => 'Admin',
        'slug' => 'admin',
        'permissions' => ['roles.view', 'roles.manage', 'users.manage', 'orders.view'],
        'is_system' => true,
    ]);

    $this->actingAs($admin)->post(route('roles-permissions.roles.store'), [
        'name' => 'Escalated role',
        'permissions' => ['orders.view', 'clients.delete'],
    ])->assertForbidden();

    $targetUser = User::factory()->create();
    $targetMembership = WorkspaceUser::create([
        'workspace_id' => $workspace->id,
        'user_id' => $targetUser->id,
        'role' => 'staff',
        'accepted_at' => now(),
    ]);
    $ownerRole = WorkspaceRole::query()->where('workspace_id', $workspace->id)->where('slug', 'owner')->firstOrFail();
    $adminRole = WorkspaceRole::query()->where('workspace_id', $workspace->id)->where('slug', 'admin')->firstOrFail();

    $this->put(route('roles-permissions.members.update', $targetMembership), ['role_id' => $ownerRole->id])->assertForbidden();
    $this->put(route('roles-permissions.members.update', $targetMembership), ['role_id' => $adminRole->id])->assertForbidden();

    $this->assertDatabaseMissing('workspace_roles', ['workspace_id' => $workspace->id, 'slug' => 'escalated-role']);
});

test('assigned module permissions are enforced on server-side resource routes', function () {
    [$owner, $workspace] = createRolesWorkspaceMember('roles-route-owner@example.com');
    [$staff] = addRolesWorkspaceMember($workspace, 'roles-route-staff@example.com', 'staff');
    WorkspaceRole::query()->create([
        'workspace_id' => $workspace->id,
        'name' => 'Staff',
        'slug' => 'staff',
        'permissions' => ['orders.view'],
        'is_system' => true,
    ]);

    $this->actingAs($staff)->get(route('orders.index'))->assertOk();
    $this->get(route('orders.create'))->assertForbidden();
    $this->post(route('orders.store'), [])->assertForbidden();
});
