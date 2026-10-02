<?php

namespace App\Models;

use App\Traits\BelongsToWorkspace;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use HasFactory, BelongsToWorkspace;

    protected $fillable = [
        'workspace_id',
        'name',
        'email',
        'phone',
        'contact_person',
        'tax_id',
        'billing_address',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

}
