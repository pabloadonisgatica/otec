<?php

namespace App\Services;

use App\Models\Evaluation;
use App\Models\Execution;

class EvaluationService
{
    /**
     * Guarda las notas finales de todos los participantes
     * de la ejecución en un solo envío.
     *
     * $grades = [participant_id => nota, ...]
     */
    public function save(
        Execution $execution,
        array $grades
    ): void {

        foreach ($execution->participants as $participant) {

            $grade = $grades[$participant->id] ?? null;

            $grade = $grade === '' ? null : $grade;

            Evaluation::updateOrCreate(
                [
                    'execution_id' => $execution->id,
                    'participant_id' => $participant->id,
                ],
                [
                    'final_grade' => $grade,
                ]
            );
        }
    }
}
