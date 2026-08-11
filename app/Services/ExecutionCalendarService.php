<?php

namespace App\Services;

use App\Models\Execution;
use App\Models\ExecutionPlanning;
use App\Models\ExecutionSession;
use App\Models\Holiday;
use Carbon\Carbon;

class ExecutionCalendarService
{
    /**
     * Genera la agenda académica.
     */
    public function generate(Execution $execution): void
    {
        $planning = $execution->planning;

        if (!$planning) {
            throw new \Exception('La ejecución no posee una planificación.');
        }

        ExecutionSession::where('execution_id', $execution->id)->delete();

        $sessions = $this->build($execution, $planning);

        foreach ($sessions as $session) {

            ExecutionSession::create([
                'execution_id' => $execution->id,
                ...$session,
            ]);
        }

        if (!empty($sessions)) {

            $execution->update([
                'end_date' => end($sessions)['session_date'],
            ]);
        }
    }

    /**
     * Vista previa.
     */
    public function preview(Execution $execution): array
    {
        $planning = $execution->planning;

        if (!$planning) {
            return [];
        }

        $sessions = $this->build($execution, $planning);

        return [

            'course_hours' => $execution->course_hours,

            'planned_hours' => collect($sessions)->sum('hours'),

            'total_sessions' => count($sessions),

            'start_date' => $planning->start_date,

            'end_date' => end($sessions)['session_date'] ?? null,

            'sessions' => $sessions,

        ];
    }

    /**
     * Construye las sesiones.
     */
    private function build(
        Execution $execution,
        ExecutionPlanning $planning
    ): array {

        $sessions = [];

        $date = Carbon::parse($planning->start_date);

        $remainingHours = $execution->course_hours;

        $weekDays = collect($planning->week_days)
            ->map(fn ($day) => (int) $day)
            ->toArray();

        while ($remainingHours > 0) {

            if (!$this->isWorkingDay($date, $planning, $weekDays)) {
                $date->addDay();
                continue;
            }

            $hours = min(
                $planning->hours_per_day,
                $remainingHours
            );

            $sessions[] = [

                'session_date' => $date->toDateString(),

                'start_time' => $planning->mode === 'manual'
                    ? null
                    : $planning->start_time,

                'end_time' => $planning->mode === 'manual'
                    ? null
                    : $this->calculateEndTime(
                        $planning->start_time,
                        $hours
                    ),

                'hours' => $hours,

            ];

            $remainingHours -= $hours;

            $date->addDay();
        }

        return $sessions;
    }

    /**
     * Determina si el día puede planificarse.
     */
    private function isWorkingDay(
        Carbon $date,
        ExecutionPlanning $planning,
        array $weekDays
    ): bool {

        /*
         Carbon:
         Monday = 1
         ...
         Sunday = 7
        */

        if (!in_array($date->dayOfWeekIso, $weekDays)) {
            return false;
        }

        if (
            $planning->exclude_holidays &&
            Holiday::whereDate('date', $date->toDateString())->exists()
        ) {
            return false;
        }

        return true;
    }

    /**
     * Calcula hora término.
     */
    private function calculateEndTime(
        string $startTime,
        float $hours
    ): string {

        return Carbon::parse($startTime)
            ->addMinutes($hours * 60)
            ->format('H:i:s');
    }
}