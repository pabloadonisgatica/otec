<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExecutionSurveyResponse extends Model
{
    protected $fillable = [
        'execution_survey_id',
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    public function executionSurvey()
    {
        return $this->belongsTo(ExecutionSurvey::class);
    }

    public function answers()
    {
        return $this->hasMany(ExecutionSurveyAnswer::class);
    }
}
