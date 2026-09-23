<?php

namespace App\Http\Controllers;

use App\Models\Execution;
use App\Models\ExecutionSurveyResponse;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ExecutionSurveyAdminController extends Controller
{
    /**
     * Borra todas las respuestas de la encuesta de esta ejecución,
     * sin tocar el ExecutionSurvey (link/QR se mantiene intacto).
     */
    public function clearResponses(Execution $execution)
    {
        $executionSurvey = $execution->executionSurvey;

        abort_unless($executionSurvey, 404);

        ExecutionSurveyResponse::where('execution_survey_id', $executionSurvey->id)->delete();

        return redirect()
            ->route('executions.show', [$execution, 'tab' => 'survey'])
            ->with('status', 'Respuestas de prueba eliminadas. La encuesta y su link/QR siguen activos.');
    }

    /**
     * Devuelve el QR como archivo SVG descargable.
     */
    public function downloadQr(Execution $execution)
    {
        $executionSurvey = $execution->executionSurvey;

        abort_unless($executionSurvey, 404);

        $svg = QrCode::format('svg')
            ->size(400)
            ->generate($executionSurvey->publicUrl());

        return response($svg, 200, [
            'Content-Type'        => 'image/svg+xml',
            'Content-Disposition' => 'attachment; filename="qr-encuesta-' . $execution->internal_code . '.svg"',
        ]);
    }
}
