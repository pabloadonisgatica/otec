<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuestionnaireQuestion extends Model
{
    public const TYPES = [
        'short_text' => 'Texto corto',
        'long_text' => 'Texto largo',
        'single_choice' => 'Opción única',
        'multiple_choice' => 'Opción múltiple',
        'dropdown' => 'Lista desplegable',
        'yes_no' => 'Sí / No',
        'scale_5' => 'Escala 1 a 5',
        'scale_10' => 'Escala 1 a 10',
        'nps' => 'NPS (0 a 10)',
        'date' => 'Fecha',
    ];

    /** Tipos que necesitan una lista de opciones. */
    public const CHOICE_TYPES = ['single_choice', 'multiple_choice', 'dropdown'];

    /** Tipos con extremos rotulados y su rango numérico. */
    public const SCALE_RANGES = [
        'scale_5' => [1, 5],
        'scale_10' => [1, 10],
        'nps' => [0, 10],
    ];

    public const MAX_OPTIONS = 50;

    protected $fillable = [
        'questionnaire_id',
        'questionnaire_section_id',
        'type',
        'label',
        'help',
        'is_required',
        'options',
        'settings',
        'sort_order',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'options' => 'array',
        'settings' => 'array',
    ];

    public function questionnaire(): BelongsTo
    {
        return $this->belongsTo(Questionnaire::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(QuestionnaireSection::class, 'questionnaire_section_id');
    }

    public function typeLabel(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    public function hasOptions(): bool
    {
        return in_array($this->type, self::CHOICE_TYPES, true);
    }

    public function isScale(): bool
    {
        return isset(self::SCALE_RANGES[$this->type]);
    }

    /** Divide el texto de opciones (una por línea), sin vacías ni repetidas. */
    public static function parseOptions(?string $text): array
    {
        return collect(preg_split('/\r\n|\r|\n/', (string) $text))
            ->map(fn ($option) => trim($option))
            ->filter()
            ->unique(fn ($option) => mb_strtolower($option))
            ->values()
            ->all();
    }
}
