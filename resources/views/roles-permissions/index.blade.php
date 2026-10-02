@extends('layouts.app')
@section('content')

<div class="space-y-6">
    <div>
        <p class="text-sm font-semibold text-blue-700 dark:text-blue-400">Workspace settings</p>
        <h1 class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">Roles &amp; Permissions</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage workspace access and role permissions.</p>
    </div>

    @if(session('success'))
        <div role="status" class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-800 dark:border-green-800 dark:bg-green-900/20 dark:text-green-300">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div role="alert" class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-800 dark:bg-red-900/20 dark:text-red-300">
            <ul class="list-inside list-disc space-y-1">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <section class="space-y-4" aria-labelledby="roles-heading">
        <div>
            <h2 id="roles-heading" class="text-lg font-bold text-gray-900 dark:text-white">Roles</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Permissions are limited to the current workspace.</p>
        </div>
        <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <table class="w-full min-w-[760px] text-left">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400">Role</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400">Users</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400">Permissions</th>
                        @if($canManageRoles)<th class="px-5 py-3.5 text-right text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400">Actions</th>@endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($roleList as $role)
                        @php
                            $summary = collect($role->permissions ?? [])->map(fn ($permission) => $permissionLabels[$permission] ?? $permission)->take(5);
                            $canEditRole = $canManageRoles && (!$role->is_system || $isOwner);
                        @endphp
                        <tr class="align-top hover:bg-gray-50/70 dark:hover:bg-gray-700/20">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-2 text-sm font-semibold text-gray-900 dark:text-white">{{ $role->name }}
                                    @if($role->is_system)<span class="rounded-full bg-gray-100 px-2 py-0.5 text-[10px] font-semibold uppercase text-gray-600 dark:bg-gray-700 dark:text-gray-300">Built-in</span>@endif
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 text-sm text-gray-600 dark:text-gray-300">{{ $memberCounts[$role->slug] ?? 0 }}</td>
                            <td class="px-5 py-4">
                                @if($role->slug === 'owner')
                                    <span class="text-sm text-gray-600 dark:text-gray-300">All workspace permissions</span>
                                @elseif($summary->isNotEmpty())
                                    <span class="text-sm text-gray-600 dark:text-gray-300">{{ $summary->implode(', ') }}@if(count($role->permissions ?? []) > 5) +{{ count($role->permissions) - 5 }} more @endif</span>
                                @else
                                    <span class="text-sm text-gray-500 dark:text-gray-400">No permissions assigned</span>
                                @endif
                            </td>
                            @if($canManageRoles)
                                <td class="px-5 py-4 text-right">
                                    @if($canEditRole)
                                        <details class="inline-block text-left">
                                            <summary class="cursor-pointer list-none rounded-md px-2 py-1.5 text-xs font-semibold text-blue-700 transition hover:bg-blue-50 dark:text-blue-300 dark:hover:bg-blue-950/50">Edit</summary>
                                            <div class="mt-2 w-[min(42rem,calc(100vw-3rem))] rounded-xl border border-gray-200 bg-white p-5 text-left shadow-xl dark:border-gray-700 dark:bg-gray-800">
                                                <form action="{{ route('roles-permissions.roles.update', $role) }}" method="POST" class="space-y-4">
                                                    @csrf @method('PUT')
                                                    <div>
                                                        <label for="role-name-{{ $role->id }}" class="mb-1 block text-sm font-semibold text-gray-700 dark:text-gray-300">Role name</label>
                                                        <input id="role-name-{{ $role->id }}" name="name" value="{{ $role->name }}" required maxlength="80" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none dark:border-gray-600 dark:bg-gray-900 dark:text-white">
                                                    </div>
                                                    <div class="grid gap-4 sm:grid-cols-2">
                                                        @foreach($permissionGroups as $group => $permissions)
                                                            <fieldset class="space-y-2">
                                                                <legend class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $group }}</legend>
                                                                @foreach($permissions as $key => $label)
                                                                    <label class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                                                                        <input type="checkbox" name="permissions[]" value="{{ $key }}" @checked(in_array($key, $role->permissions ?? [], true)) @disabled(!$isOwner && !in_array($key, $actorPermissions, true)) class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-900">
                                                                        {{ $label }}
                                                                    </label>
                                                                @endforeach
                                                            </fieldset>
                                                        @endforeach
                                                    </div>
                                                    <div class="flex justify-end">
                                                        <button class="rounded-lg bg-blue-600 px-3.5 py-2 text-sm font-semibold text-white transition hover:bg-blue-700">Save role</button>
                                                    </div>
                                                </form>
                                                @if(!$role->is_system)
                                                    <form action="{{ route('roles-permissions.roles.destroy', $role) }}" method="POST" class="mt-2 text-right" onsubmit="return confirm('Delete this role?')">
                                                        @csrf @method('DELETE')
                                                        <button class="rounded-md px-2 py-1.5 text-xs font-semibold text-red-600 transition hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-950/40">Delete role</button>
                                                    </form>
                                                @endif
                                            </div>
                                        </details>
                                    @else
                                        <span class="text-xs text-gray-400 dark:text-gray-500">Owner only</span>
                                    @endif
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    @if($canManageRoles)
        <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800 sm:p-6" aria-labelledby="create-role-heading">
            <h2 id="create-role-heading" class="text-base font-bold text-gray-900 dark:text-white">Create Role</h2>
            <form action="{{ route('roles-permissions.roles.store') }}" method="POST" class="mt-4 space-y-5">
                @csrf
                <div class="max-w-md">
                    <label for="new-role-name" class="mb-1.5 block text-sm font-semibold text-gray-700 dark:text-gray-300">Role name</label>
                    <input id="new-role-name" name="name" value="{{ old('name') }}" required maxlength="80" class="w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-sm text-gray-900 focus:border-blue-500 focus:outline-none dark:border-gray-600 dark:bg-gray-900 dark:text-white">
                </div>
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach($permissionGroups as $group => $permissions)
                        <fieldset class="space-y-2">
                            <legend class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $group }}</legend>
                            @foreach($permissions as $key => $label)
                                <label class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                                    <input type="checkbox" name="permissions[]" value="{{ $key }}" @checked(in_array($key, old('permissions', []), true)) @disabled(!$isOwner && !in_array($key, $actorPermissions, true)) class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-900">
                                    {{ $label }}
                                </label>
                            @endforeach
                        </fieldset>
                    @endforeach
                </div>
                <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700">Create Role</button>
            </form>
        </section>
    @endif

    <section class="space-y-4" aria-labelledby="members-heading">
        <div>
            <h2 id="members-heading" class="text-lg font-bold text-gray-900 dark:text-white">Workspace members</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Roles apply only to members of this workspace.</p>
        </div>
        <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <table class="w-full min-w-[640px] text-left">
                <thead class="bg-gray-50 dark:bg-gray-900/50"><tr class="border-b border-gray-200 dark:border-gray-700">
                    <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400">User</th>
                    <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400">Email</th>
                    <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400">Current role</th>
                    @if($canManageUsers)<th class="px-5 py-3.5 text-right text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400">Actions</th>@endif
                </tr></thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($memberList as $member)
                        @php $currentRole = $roleList->firstWhere('slug', $member->role); @endphp
                        <tr>
                            <td class="px-5 py-4 text-sm font-semibold text-gray-900 dark:text-white">{{ $member->user?->name ?? 'Unavailable user' }}</td>
                            <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-300">{{ $member->user?->email ?? '—' }}</td>
                            <td class="px-5 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $currentRole?->name ?? ucfirst($member->role) }}@if(!$member->accepted_at) <span class="ml-1 text-xs text-amber-700 dark:text-amber-300">Invited</span>@endif</td>
                            @if($canManageUsers)
                                <td class="px-5 py-4 text-right">
                                    @if($assignableRoles->isNotEmpty() && ($isOwner || !in_array($member->role, ['owner', 'admin'], true)))
                                        <form action="{{ route('roles-permissions.members.update', $member) }}" method="POST" class="flex items-center justify-end gap-2">
                                            @csrf @method('PUT')
                                            <select name="role_id" aria-label="Role for {{ $member->user?->name }}" class="max-w-40 rounded-lg border border-gray-300 bg-white px-2.5 py-2 text-sm text-gray-800 dark:border-gray-600 dark:bg-gray-900 dark:text-white">
                                                @foreach($assignableRoles as $assignableRole)
                                                    <option value="{{ $assignableRole->id }}" @selected($member->role === $assignableRole->slug)>{{ $assignableRole->name }}</option>
                                                @endforeach
                                            </select>
                                            <button class="rounded-md px-2 py-1.5 text-xs font-semibold text-blue-700 transition hover:bg-blue-50 dark:text-blue-300 dark:hover:bg-blue-950/50">Save</button>
                                        </form>
                                    @else
                                        <span class="text-xs text-gray-400 dark:text-gray-500">Owner only</span>
                                    @endif
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
</div>

@endsection