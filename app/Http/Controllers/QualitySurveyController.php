<?php

namespace App\Http\Controllers;

use App\Models\Execution;
use App\Models\Instructor;
use App\Models\SurveyAnswer;
use App\Models\SurveyQuestion;
use App\Models\SurveyResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class QualitySurveyController extends Controller
{
    /**
     * Tabla consolidada: una fila por Ejecución + Relator, mostrando
     * el estado de ambos tipos de evaluación (Participantes / Gerencia).
     */
    public function index(Request $request)
    {
        $type = $request->string('type')->toString(); // '', 'participante', 'gerencia'

        $executions = Execution::with(['instructors', 'course'])
            ->whereHas('instructors')
            ->orderByDesc('start_date')
            ->get();

        $rows = collect();

        foreach ($executions as $execution) {
            foreach ($execution->instructors as $instructor) {

                $participantStats = SurveyResponse::where('execution_id', $execution->id)
                    ->where('evaluator_type', 'participante')
                    ->whereNotNull('submitted_at')
                    ->count();

                $managementEvaluation = SurveyResponse::where('execution_id', $execution->id)
                    ->where('evaluator_type', 'gerencia')
                    ->where('instructor_id', $instructor->id)
                    ->whereNotNull('submitted_at')
                    ->latest('submitted_at')
                    ->first();

                $rows->push([
                    'execution' => $execution,
                    'instructor' => $instructor,
                    'participant_responses_count' => $participantStats,
                    'management_evaluation' => $managementEvaluation,
                ]);
            }
        }

        if ($type === 'participante') {
            $rows = $rows->filter(fn ($r) => $r['participant_responses_count'] > 0);
        } elseif ($type === 'gerencia') {
            $rows = $rows->filter(fn ($r) => $r['management_evaluation'] !== null);
        }

        $rows = $rows->values();

        return view('quality.surveys.index', [
            'rows' => $rows,
            'type' => $type,
        ]);
    }

    /**
     * Formulario para que Gerencia evalúe a un Relator en una Ejecución
     * específica — mismas preguntas del área "Al Instructor".
     */
    public function createManagementEvaluation(Execution $execution, Instructor $instructor)
    {
        $questions = SurveyQuestion::where('area', 'A. Al Instructor')
            ->orderBy('sort_order')
            ->get();

        return view('quality.surveys.evaluate', compact('execution', 'instructor', 'questions'));
    }

    public function storeManagementEvaluation(Request $request, Execution $execution, Instructor $instructor)
    {
        $validated = $request->validate([
            'evaluator_name' => ['required', 'string', 'max:255'],
            'scores' => ['required', 'array'],
            'suggestions' => ['nullable', 'string'],
        ]);

        $response = SurveyResponse::create([
            'execution_id' => $execution->id,
            'evaluator_type' => 'gerencia',
            'instructor_id' => $instructor->id,
            'evaluator_name' => $validated['evaluator_name'],
            'token' => Str::random(40),
            'suggestions' => $validated['suggestions'] ?? null,
            'submitted_at' => now(),
        ]);

        foreach ($validated['scores'] as $questionId => $score) {
            SurveyAnswer::create([
                'survey_response_id' => $response->id,
                'survey_question_id' => $questionId,
                'score' => $score !== '' ? $score : null,
            ]);
        }

        return redirect()
            ->route('quality.surveys.index')
            ->with('status', 'Evaluación de Gerencia registrada correctamente.');
    }

    public function showManagementEvaluation(SurveyResponse $response)
    {
        abort_unless($response->evaluator_type === 'gerencia', 404);

        $response->load(['answers.question', 'execution', 'instructor']);

        return view('quality.surveys.show', compact('response'));
    }
}
