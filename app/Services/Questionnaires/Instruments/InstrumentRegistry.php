<?php

namespace App\Services\Questionnaires\Instruments;

use App\Enums\QuestionnaireType;
use App\Models\Questionnaire;

/**
 * Instrumento que corresponde a cada tipo de encuesta.
 * Un tipo sin implementación aún se muestra como "disponible pronto".
 */
class InstrumentRegistry
{
    public static function for(?QuestionnaireType $type, ?Questionnaire $questionnaire = null): ?Instrument
    {
        return match ($type) {
            QuestionnaireType::GroupEvaluation => new GroupEvaluationInstrument(),
            QuestionnaireType::Leadership => new LeadershipInstrument(),
            QuestionnaireType::Satisfaction => ! empty($questionnaire?->settings['template'])
                ? new SatisfactionInstrument($questionnaire->settings)
                : null,
            default => null,
        };
    }
}
