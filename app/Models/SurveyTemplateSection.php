<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurveyTemplateSection extends Model
{
    protected $fillable = [
        'survey_template_id',
        'title',
        'description',
        'repeats_per_instructor',
        'sort_order',
    ];

    protected $casts = [
        'repeats_per_instructor' => 'boolean',
    ];

    public function template()
    {
        return $this->belongsTo(SurveyTemplate::class, 'survey_template_id');
    }

    public function fields()
    {
        return $this->hasMany(SurveyTemplateField::class)
            ->orderBy('sort_order');
    }
}
