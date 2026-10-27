<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuestionnaireAnswer extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'questionnaire_response_id',
        'questionnaire_question_id',
        'question_label',
        'question_type',
        'value',
        'choices',
        'numeric_value',
    ];

    protected $casts = [
        'choices' => 'array',
        'numeric_value' => 'integer',
    ];

    public function response(): BelongsTo
    {
        return $this->belongsTo(QuestionnaireResponse::class, 'questionnaire_response_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(QuestionnaireQuestion::class, 'questionnaire_question_id');
    }

    /** Valor para mostrar: las opciones múltiples se unen con coma. */
    public function displayValue(): string
    {
        return $this->choices ? implode(', ', $this->choices) : (string) $this->value;
    }
}
