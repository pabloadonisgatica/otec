<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurveyTemplateField extends Model
{
    protected $fillable = [
        'survey_template_section_id',
        'label',
        'type',
        'options',
        'required',
        'sort_order',
    ];

    protected $casts = [
        'options' => 'array',
        'required' => 'boolean',
    ];

    public const TYPES = [
        'input' => 'Texto corto',
        'radio' => 'Opción única (botones)',
        'select' => 'Lista desplegable',
        'textarea' => 'Texto largo',
    ];

    public function section()
    {
        return $this->belongsTo(SurveyTemplateSection::class, 'survey_template_section_id');
    }
}
