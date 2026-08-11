<?php

namespace App\Http\Controllers;

use App\Models\Execution;
use App\Services\SurveyService;

class ExecutionSurveyController extends Controller
{
    /**
     * Enviar (o reenviar) las invitaciones de la encuesta
     * a los participantes de la ejecución.
     */
    public function send(
        Execution $execution,
        SurveyService $surveyService
    ) {
        $sent = $surveyService->sendInvitations($execution);

        $message = $sent > 0
            ? "Encuesta enviada a {$sent} participante(s)."
            : 'No había nuevos participantes a quienes enviar (ya invitados o sin correo registrado).';

        return redirect()
            ->route('executions.show', [
                $execution,
                'tab' => 'survey',
            ])
            ->with('status', $message);
    }
}
