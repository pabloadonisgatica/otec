<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BudgetCourse extends Model
{
    protected $fillable = [
        'budget_id',
        'course_id',
        'participants',
        'hours',
        'unit_price',
        'discount_percent',
        'instructor_id',
        'line_total',
        'sort_order',
    ];

    public function budget(): BelongsTo
    {
        return $this->belongsTo(Budget::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(Instructor::class);
    }
}
