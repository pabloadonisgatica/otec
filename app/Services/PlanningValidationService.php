<?php

namespace App\Services;

use App\Models\Execution;

class PlanningValidationService
{
    public function validate(Execution $execution): array
    {
        $plannedHours = $execution->sessions()->sum('hours');

        $requiredHours = $execution->course_hours;

        $remainingHours = max(
            0,
            $requiredHours - $plannedHours
        );

        $excessHours = max(
            0,
            $plannedHours - $requiredHours
        );

        if ((float) $plannedHours === 0.0) {

            $status = 'not_planned';

        } elseif ($plannedHours < $requiredHours) {

            $status = 'incomplete';

        } elseif ($plannedHours > $requiredHours) {

            $status = 'exceeded';

        } else {

            $status = 'complete';
        }

        return [

            'status' => $status,

            'required_hours' => $requiredHours,

            'planned_hours' => $plannedHours,

            'remaining_hours' => $remainingHours,

            'excess_hours' => $excessHours,

            'total_sessions' => $execution->sessions()->count(),

            'can_generate' => (float) $plannedHours === 0.0,

            'can_regenerate' => true,

            'can_start_execution' => $status === 'complete',

        ];
    }
}