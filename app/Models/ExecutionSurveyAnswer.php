<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExecutionSurveyAnswer extends Model
{
    protected $fillable = [
        'execution_survey_response_id',
        'survey_template_field_id',
        'instructor_id',
        'value',
    ];

    public function response()
    {
        return $this->belongsTo(ExecutionSurveyResponse::class, 'execution_survey_response_id');
    }

    public function field()
    {
        return $this->belongsTo(SurveyTemplateField::class, 'survey_template_field_id');
    }

    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }
}
