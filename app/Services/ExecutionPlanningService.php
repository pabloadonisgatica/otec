<?php

namespace App\Services;

use App\Models\Execution;
use App\Models\ExecutionPlanning;

class ExecutionPlanningService
{
    /**
     * Crea o actualiza la planificación
     * de una ejecución.
     */
    public function save(
        Execution $execution,
        array $data
    ): ExecutionPlanning {

        return ExecutionPlanning::updateOrCreate(

            [
                'execution_id' => $execution->id,
            ],

            [
                'mode' => $data['mode'],

                'shift' => $data['shift'] ?? null,

                'start_date' => $data['start_date'],

                'start_time' => $data['start_time'] ?? null,

                'hours_per_day' => $data['hours_per_day'],

                'week_days' => $data['week_days'],

                'exclude_holidays' => $data['exclude_holidays'] ?? true,
            ]

        );
    }

    /**
     * Obtiene la planificación vigente.
     */
    public function get(
        Execution $execution
    ): ?ExecutionPlanning {

        return $execution
            ->planning()
            ->first();
    }

    /**
     * Elimina la planificación.
     */
    public function delete(
        Execution $execution
    ): void {

        $execution
            ->planning()
            ->delete();
    }
}