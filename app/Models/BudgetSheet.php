<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BudgetSheet extends Model
{
    protected $fillable = [
        'budget_id',
        'version',
        'inputs',
        'outputs',
    ];

    protected $casts = [
        'inputs' => 'array',
        'outputs' => 'array',
    ];

    public function budget(): BelongsTo
    {
        return $this->belongsTo(Budget::class);
    }
}
