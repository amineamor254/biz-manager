<?php

namespace App\Models;

use App\Traits\BelongsToWorkspace;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory, BelongsToWorkspace;

    public const CATEGORIES = [
        'Utilities',
        'Supplies',
        'Transport',
        'Rent',
        'Marketing',
        'Salaries',
        'Other',
    ];

    protected $fillable = [
        'description',
        'amount',
        'category',
        'expense_date',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
    ];
}