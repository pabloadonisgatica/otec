<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExecutionSurvey extends Model
{
    protected $fillable = [
        'execution_id',
        'survey_template_id',
        'token',
    ];

    public function execution()
    {
        return $this->belongsTo(Execution::class);
    }

    public function template()
    {
        return $this->belongsTo(SurveyTemplate::class, 'survey_template_id');
    }

    public function responses()
    {
        return $this->hasMany(ExecutionSurveyResponse::class);
    }

    public function publicUrl(): string
    {
        return route('execution-survey.public.show', $this->token);
    }
}
