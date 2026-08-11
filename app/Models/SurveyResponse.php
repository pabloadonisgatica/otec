<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurveyResponse extends Model
{
    protected $fillable = [
        'execution_id',
        'participant_id',
        'token',
        'suggestions',
        'submitted_at',
        'invited_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'invited_at' => 'datetime',
    ];

    public function execution()
    {
        return $this->belongsTo(Execution::class);
    }

    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }

    public function answers()
    {
        return $this->hasMany(SurveyAnswer::class);
    }

    public function isSubmitted(): bool
    {
        return $this->submitted_at !== null;
    }
}
