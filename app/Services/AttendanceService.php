<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Execution;
use App\Models\ExecutionSession;

class AttendanceService
{
    /**
     * Guarda la asistencia de una sesión.
     *
     * Recorre a todos los participantes inscritos en la
     * ejecución (no solo los marcados) para que quede un
     * registro explícito de presente/ausente por cada uno.
     */
    public function save(
        ExecutionSession $session,
        array $presentParticipantIds
    ): void {

        $participants = $session->execution->participants;

        foreach ($participants as $participant) {

            Attendance::updateOrCreate(
                [
                    'execution_session_id' => $session->id,
                    'participant_id' => $participant->id,
                ],
                [
                    'present' => in_array(
                        $participant->id,
                        $presentParticipantIds
                    ),
                ]
            );
        }
    }

    /**
     * Guarda la asistencia de todas las sesiones de la
     * ejecución en un solo envío (matriz participante x sesión).
     *
     * $matrix = [
     *     session_id => [participant_id, participant_id, ...],
     * ]
     */
    public function saveMatrix(
        Execution $execution,
        array $matrix
    ): void {

        foreach ($execution->sessions as $session) {

            $presentIds = array_map(
                'intval',
                $matrix[$session->id] ?? []
            );

            $this->save($session, $presentIds);
        }
    }
}
