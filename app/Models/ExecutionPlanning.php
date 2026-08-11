<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExecutionPlanning extends Model
{
    protected $fillable = [
        'execution_id',
        'mode',
        'shift',
        'start_date',
        'start_time',
        'hours_per_day',
        'week_days',
        'exclude_holidays',
    ];

    protected $casts = [
    'start_date' => 'date',
    'week_days' => 'array',
    'exclude_holidays' => 'boolean',
];    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function execution()
    {
        return $this->belongsTo(Execution::class);
    }
}