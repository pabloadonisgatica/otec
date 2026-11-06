<?php

namespace App\Http\Controllers;

use App\Models\ExecutionSurvey;
use App\Services\ExecutionSurveySubmissionService;
use Illuminate\Http\Request;

/**
 * Encuesta de satisfacción pública de una ejecución (link/QR, sin login).
 * El participante elige su relator y evalúa solo a ese relator.
 */
class PublicExecutionSurveyController extends Controller
{
    public function __construct(private ExecutionSurveySubmissionService $submissions)
    {
    }

    public function show(string $token)
    {
        $executionSurvey = $this->find($token);

        return view('execution_survey.show', [
            'executionSurvey' => $executionSurvey,
            'execution' => $executionSurvey->execution,
            'sections' => $executionSurvey->template->sections,
            'instructors' => $this->submissions->instructors($executionSurvey),
        ]);
    }

    public function store(Request $request, string $token)
    {
        $executionSurvey = $this->find($token);

        // Sin relatores asignados no hay a quién evaluar (la página muestra un aviso).
        if ($this->submissions->instructors($executionSurvey)->isEmpty()) {
            return redirect()->route('execution-survey.public.show', $token);
        }

        $data = $request->validate(
            $this->submissions->rules($executionSurvey),
            $this->submissions->messages($executionSurvey)
        );

        $this->submissions->store(
            $executionSurvey,
            (int) $data['instructor_id'],
            $data['answers'] ?? []
        );

        // PRG: recargar la página de gracias no vuelve a enviar.
        return redirect()->route('execution-survey.public.thanks', $token);
    }

    public function thanks(string $token)
    {
        $executionSurvey = ExecutionSurvey::where('token', $token)
            ->with('execution.course')
            ->firstOrFail();

        return view('execution_survey.thanks', [
            'execution' => $executionSurvey->execution,
        ]);
    }

    private function find(string $token): ExecutionSurvey
    {
        return ExecutionSurvey::where('token', $token)
            ->with(['template.sections.fields', 'execution.instructors', 'execution.course'])
            ->firstOrFail();
    }
}
