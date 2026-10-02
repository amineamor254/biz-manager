<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'current_workspace_id',
        'role',
        'verification_code',
        'last_login_at',
        'settings',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'settings' => 'json',
        ];
    }

    // User owns workspaces
    public function ownedWorkspaces(): HasMany
    {
        return $this->hasMany(Workspace::class);
    }

    // Pivot memberships (SAFE)
    public function workspaceMembers(): HasMany
    {
        return $this->hasMany(WorkspaceUser::class);
    }

    // Get current workspace (SAFE)
    public function currentWorkspace()
    {
        return $this->belongsTo(Workspace::class, 'current_workspace_id');
    }

    public function workspaces(): BelongsToMany
    {
        return $this->belongsToMany(Workspace::class, 'workspace_users')
            ->withPivot(['role', 'invited_at', 'accepted_at']);
    }

    public function getCurrentWorkspace(): ?Workspace
    {
        if (!$this->current_workspace_id) {
            return null;
        }

        return $this->workspaces()
            ->whereKey($this->current_workspace_id)
            ->wherePivotNotNull('accepted_at')
            ->first();
    }

    public function getRoleInWorkspace(Workspace $workspace): ?string
    {
        $membership = $this->workspaceMembers()
            ->where('workspace_id', $workspace->id)
            ->whereNotNull('accepted_at')
            ->first();

        return $membership?->role;
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}