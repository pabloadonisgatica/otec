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

        $template = SurveyTemplate::with('sections')->findOrFail($data['survey_template_id']);

        // Si la plantilla tiene secciones que se repiten por relator,
        // la ejecución DEBE tener al menos un relator asignado.
        $hasRepeatingSection = $template->sections->contains('repeats_per_instructor', true);

        if ($hasRepeatingSection) {
            $execution->loadMissing('instructors');

            if ($execution->instructors->isEmpty()) {
                return back()->with(
                    'error',
                    'Esta plantilla tiene secciones de evaluación de relatores, pero la ejecución no tiene ningún relator asignado. Asigna al menos un relator antes de generar la encuesta.'
                );
            }
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
