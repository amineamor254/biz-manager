<?php

namespace App\Models;

use App\Traits\BelongsToWorkspace;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use HasFactory, BelongsToWorkspace;

    protected $fillable = [
        'workspace_id',
        'client_id',
        'invoice_number',
        'status',
        'total',
        'date',
        'due_date',
        'payment_method',
        'payment_received_at',
        'notes',
        'terms',
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'date' => 'date',
        'due_date' => 'date',
        'payment_received_at' => 'datetime',
    ];

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid' && $this->payment_received_at !== null;
    }

    public function isOverdue(): bool
    {
        return $this->status === 'overdue' && $this->due_date && $this->due_date->isPast();
    }

    public function getFormattedNumber(): string
    {
        return sprintf("INV-%s-%05d", $this->workspace->slug ?? 'DEF', $this->id);
    }
}
