<?php

namespace App\Http\Controllers;

use App\Models\Execution;
use App\Models\ExecutionSurvey;
use App\Models\SurveyTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ExecutionSurveyTemplateController extends Controller
{
    /**
     * Genera la encuesta pública de la ejecución a partir de una plantilla.
     * Valida antes de crear que la ejecución tenga los datos necesarios.
     */
    public function store(Request $request, Execution $execution)
    {
        $data = $request->validate([
            'survey_template_id' => ['required', 'exists:survey_templates,id'],
        ]);

        if ($execution->executionSurvey) {
            return back()->with('error', 'Esta ejecución ya tiene una encuesta generada.');
        }

        SurveyTemplate::findOrFail($data['survey_template_id']);

        // Cada participante elige y evalúa a su relator: sin relatores no hay a quién evaluar.
        if ($execution->instructors()->doesntExist()) {
            return back()->with(
                'error',
                'La ejecución no tiene relatores asignados. Asigna al menos un relator antes de generar la encuesta.'
            );
        }

        ExecutionSurvey::create([
            'execution_id'       => $execution->id,
            'survey_template_id' => $data['survey_template_id'],
            'token'              => Str::random(48),
        ]);

        return redirect()
            ->route('executions.show', [$execution, 'tab' => 'survey'])
            ->with('status', 'Encuesta generada correctamente.');
    }

    /**
     * Elimina la encuesta generada (y sus respuestas) para poder
     * volver a elegir plantilla, si fue un error.
     */
    public function destroy(Execution $execution)
    {
        $execution->executionSurvey?->delete();

        return redirect()
            ->route('executions.show', [$execution, 'tab' => 'survey'])
            ->with('status', 'Encuesta eliminada. Puedes generar una nueva.');
    }
}
