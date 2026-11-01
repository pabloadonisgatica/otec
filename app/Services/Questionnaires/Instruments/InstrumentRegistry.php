<?php

namespace App\Services\Questionnaires\Instruments;

use App\Enums\QuestionnaireType;

/**
 * Instrumento que corresponde a cada tipo de encuesta.
 * Un tipo sin implementación aún se muestra como "disponible pronto".
 */
class InstrumentRegistry
{
    public static function for(?QuestionnaireType $type): ?Instrument
    {
        return match ($type) {
            QuestionnaireType::GroupEvaluation => new GroupEvaluationInstrument(),
            default => null,
        };
    }
}
