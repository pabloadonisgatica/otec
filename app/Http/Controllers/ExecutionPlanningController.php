<?php

namespace App\Http\Controllers;

use App\Models\Execution;
use App\Services\ExecutionPlanningService;
use App\Services\ExecutionCalendarService;
use Illuminate\Http\Request;

class ExecutionPlanningController extends Controller
{
    /**
     * Guardar la planificación.
     */
    public function update(
        Request $request,
        Execution $execution,
        ExecutionPlanningService $planningService
    ) {
        abort_if(
            $execution->isFinalized(),
            403,
            'La ejecución está finalizada. Reábrela para poder editar la planificación.'
        );

        $validated = $request->validate([

            'mode' => ['required', 'in:automatic,manual'],

            'shift' => ['required_if:mode,automatic', 'nullable', 'in:morning,afternoon'],

            'start_date' => ['required', 'date'],

            'start_time' => ['required_if:mode,automatic', 'nullable'],

            'hours_per_day' => ['required', 'numeric', 'min:1'],

            'week_days' => ['required', 'array'],

            'exclude_holidays' => ['nullable','boolean'],

        ]);

        $planningService->save(
            $execution,
            [
                ...$validated,
                'exclude_holidays' => $request->boolean('exclude_holidays'),
            ]
        );

        return redirect()
            ->route('executions.show', [
                $execution,
                'tab' => 'planning',
            ])
            ->with(
                'status',
                'Planificación guardada correctamente.'
            );
    }

    /**
     * Generar agenda.
     */
    public function generate(
        Execution $execution,
        ExecutionCalendarService $calendarService
    ) {
        abort_if(
            $execution->isFinalized(),
            403,
            'La ejecución está finalizada. Reábrela para poder generar la agenda.'
        );

        $calendarService->generate($execution);

        return redirect()
            ->route('executions.show', [
                $execution,
                'tab' => 'sessions',
            ])
            ->with(
                'status',
                'Agenda generada correctamente.'
            );
    }
}