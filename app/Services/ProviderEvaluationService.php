<?php

namespace App\Services;

use App\Models\Provider;
use App\Models\ProviderEvaluation;

/**
 * Registro de evaluaciones de proveedores y cálculo de su resultado.
 */
class ProviderEvaluationService
{
    public function create(Provider $provider, array $data): ProviderEvaluation
    {
        return $provider->evaluations()->create($this->prepare($data));
    }

    public function update(ProviderEvaluation $evaluation, array $data): ProviderEvaluation
    {
        $evaluation->update($this->prepare($data));

        return $evaluation;
    }

    /**
     * Puntaje en porcentaje: promedio de los criterios sobre la nota máxima.
     */
    public function score(array $scores): float
    {
        $values = array_map('intval', array_intersect_key($scores, ProviderEvaluation::CRITERIA));

        if ($values === []) {
            return 0.0;
        }

        $max = max(array_keys(ProviderEvaluation::SCALE));

        return round(array_sum($values) / (count($values) * $max) * 100, 2);
    }

    public function result(float $score): string
    {
        return match (true) {
            $score >= ProviderEvaluation::APPROVED_FROM => 'approved',
            $score >= ProviderEvaluation::CONDITIONAL_FROM => 'conditional',
            default => 'rejected',
        };
    }

    private function prepare(array $data): array
    {
        $scores = array_map('intval', array_intersect_key($data['scores'], ProviderEvaluation::CRITERIA));
        $score = $this->score($scores);

        return [
            ...$data,
            'scores' => $scores,
            'score' => $score,
            'result' => $this->result($score),
        ];
    }
}
