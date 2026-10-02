<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'workspace_id',
        'generated_by',
        'type',
        'title',
        'description',
        'report_data',
        'period_start',
        'period_end',
        'expires_at',
    ];

    protected $casts = [
        'report_data' => 'json',
        'period_start' => 'date',
        'period_end' => 'date',
        'expires_at' => 'datetime',
    ];

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function generatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }
}
