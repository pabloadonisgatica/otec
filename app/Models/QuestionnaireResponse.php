<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuestionnaireResponse extends Model
{
    protected $fillable = [
        'questionnaire_id',
        'first_name',
        'last_name',
        'email',
        'organization',
        'result',
    ];

    protected $casts = [
        'result' => 'array',
    ];

    public function questionnaire(): BelongsTo
    {
        return $this->belongsTo(Questionnaire::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(QuestionnaireAnswer::class);
    }

    public function respondentName(): ?string
    {
        return trim($this->first_name . ' ' . $this->last_name) ?: null;
    }

    public function submittedAtLocal(): string
    {
        return $this->created_at->copy()->timezone(Questionnaire::TIMEZONE)->format('d-m-Y H:i');
    }
}
