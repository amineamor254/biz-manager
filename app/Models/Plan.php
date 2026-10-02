<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'price',
        'billing_cycle',
        'features',
    ];

    protected $casts = [
        'features' => 'json',
    ];

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    // Helper: Get feature limit
    public function getFeature(string $featureName): mixed
    {
        return $this->features[$featureName] ?? null;
    }

    public function hasFeature(string $featureName): bool
    {
        return isset($this->features[$featureName]) && $this->features[$featureName];
    }
}
