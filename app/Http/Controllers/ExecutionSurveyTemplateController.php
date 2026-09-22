<?php

namespace App\Http\Controllers;

use App\Models\Execution;
use App\Models\ExecutionSurvey;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ExecutionSurveyTemplateController extends Controller
{
    /**
     * Genera la encuesta pública de la ejecución a partir de una plantilla.
     */
    public function store(Request $request, Execution $execution)
    {
        $data = $request->validate([
            'survey_template_id' => ['required', 'exists:survey_templates,id'],
        ]);

        if ($execution->executionSurvey) {
            return back()->with('status', 'Esta ejecución ya tiene una encuesta generada.');
        }

        ExecutionSurvey::create([
            'execution_id' => $execution->id,
            'survey_template_id' => $data['survey_template_id'],
            'token' => Str::random(48),
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
