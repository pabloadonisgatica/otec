<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Budget extends Model
{
    protected $fillable = [
        'company_id',
        'internal_name',
        'budget_date',
        'status',
        'course_code',
        'total_amount',
        'observations',
        'includes',
        'excludes',
        'created_by',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function courses(): HasMany
    {
        return $this->hasMany(BudgetCourse::class);
    }

    public function sheet(): HasOne
    {
        return $this->hasOne(BudgetSheet::class);
    }
}
