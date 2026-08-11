<?php

namespace App\Http\Controllers;

use App\Models\SurveyQuestion;
use App\Models\SurveyResponse;
use App\Services\SurveyService;
use Illuminate\Http\Request;

class SurveyController extends Controller
{
    /**
     * Mostrar el formulario de la encuesta (público, vía token).
     */
    public function show(string $token)
    {
        $response = SurveyResponse::where('token', $token)
            ->with(['execution', 'participant'])
            ->firstOrFail();

        if ($response->isSubmitted()) {
            return view('survey.thank-you', [
                'participant' => $response->participant,
                'execution' => $response->execution,
            ]);
        }

        $questions = SurveyQuestion::orderBy('sort_order')->get()->groupBy('area');

        return view('survey.show', [
            'response' => $response,
            'execution' => $response->execution,
            'participant' => $response->participant,
            'questions' => $questions,
        ]);
    }

    /**
     * Guardar las respuestas.
     */
    public function store(
        Request $request,
        string $token,
        SurveyService $surveyService
    ) {
        $response = SurveyResponse::where('token', $token)->firstOrFail();

        if ($response->isSubmitted()) {
            return redirect()->route('survey.show', $token);
        }

        $questionIds = SurveyQuestion::pluck('id');

        $validated = $request->validate([
            'scores' => ['required', 'array'],
            'suggestions' => ['nullable', 'string', 'max:2000'],
        ]);

        $scores = [];

        foreach ($questionIds as $questionId) {

            $value = $validated['scores'][$questionId] ?? null;

            $scores[$questionId] = ($value === '' || $value === null)
                ? null
                : (int) $value;
        }

        $surveyService->submit(
            $response,
            $scores,
            $validated['suggestions'] ?? null
        );

        return redirect()->route('survey.show', $token);
    }
}
