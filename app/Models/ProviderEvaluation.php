<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Evaluación de un proveedor (NCH 2728 — 7.4).
 *
 * Cada criterio se califica de 1 a 5. El puntaje es el promedio expresado
 * en porcentaje y el resultado se calcula con los umbrales de abajo.
 */
class ProviderEvaluation extends Model
{
    public const CRITERIA = [
        'quality' => [
            'label' => 'Calidad del producto o servicio',
            'help' => 'Lo entregado cumple lo solicitado, sin fallas ni reclamos.',
        ],
        'timeliness' => [
            'label' => 'Cumplimiento de plazos',
            'help' => 'Entrega en la fecha y hora comprometidas.',
        ],
        'price' => [
            'label' => 'Precio y condiciones comerciales',
            'help' => 'Precio competitivo, condiciones de pago y respeto de lo cotizado.',
        ],
        'service' => [
            'label' => 'Atención y comunicación',
            'help' => 'Responde a tiempo, resuelve problemas y avisa los imprevistos.',
        ],
        'compliance' => [
            'label' => 'Cumplimiento de requisitos',
            'help' => 'Documentación al día, factura correcta, requisitos legales y de seguridad.',
        ],
    ];

    public const SCALE = [
        1 => 'Deficiente',
        2 => 'Insuficiente',
        3 => 'Aceptable',
        4 => 'Bueno',
        5 => 'Excelente',
    ];

    public const TYPES = [
        'selection' => 'Selección (primera evaluación)',
        'reevaluation' => 'Reevaluación periódica',
    ];

    /** Puntaje mínimo (%) para cada resultado. */
    public const APPROVED_FROM = 80;

    public const CONDITIONAL_FROM = 60;

    public const RESULTS = [
        'approved' => 'Aprobado',
        'conditional' => 'Aprobado con observaciones',
        'rejected' => 'No aprobado',
    ];

    public const RESULT_BADGES = [
        'approved' => 'bg-green-50 text-green-700',
        'conditional' => 'bg-amber-50 text-amber-700',
        'rejected' => 'bg-red-50 text-red-700',
    ];

    /** Meses por defecto hasta la próxima reevaluación. */
    public const DEFAULT_REEVALUATION_MONTHS = 12;

    protected $fillable = [
        'provider_id',
        'type',
        'evaluated_at',
        'evaluator_id',
        'scores',
        'score',
        'result',
        'observations',
        'next_evaluation_at',
    ];

    protected $casts = [
        'evaluated_at' => 'date',
        'next_evaluation_at' => 'date',
        'scores' => 'array',
        'score' => 'decimal:2',
    ];

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }

    public function typeLabel(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    public function resultLabel(): string
    {
        return self::RESULTS[$this->result] ?? $this->result;
    }

    public function resultBadge(): string
    {
        return self::RESULT_BADGES[$this->result] ?? 'bg-gray-100 text-gray-600';
    }

    /** Puntaje con formato chileno: "86,7 %". */
    public function scoreLabel(): string
    {
        return number_format((float) $this->score, 1, ',', '.').' %';
    }

    public function scoreFor(string $criterion): ?int
    {
        $value = $this->scores[$criterion] ?? null;

        return $value === null ? null : (int) $value;
    }

    public function isReevaluationOverdue(): bool
    {
        return $this->next_evaluation_at !== null && $this->next_evaluation_at->isPast()
            && ! $this->next_evaluation_at->isToday();
    }
}
