<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workspace extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'domain',
        'timezone',
        'currency',
        'logo_path',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Owner
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Members (pivot only - SAFE)
    public function members()
    {
        return $this->hasMany(WorkspaceUser::class);
    }

    // Business data (IMPORTANT: keep simple)
    public function clients()
    {
        return $this->hasMany(Client::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}