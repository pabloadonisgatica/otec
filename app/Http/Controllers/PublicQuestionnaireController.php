<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubmitQuestionnaireRequest;
use App\Models\AppSetting;
use App\Models\Questionnaire;
use App\Services\Questionnaires\QuestionnaireResponseService;

/**
 * Página pública de una encuesta (sin login, identificada por token).
 */
class PublicQuestionnaireController extends Controller
{
    public function __construct(private QuestionnaireResponseService $responses)
    {
    }

    public function show(Questionnaire $questionnaire)
    {
        if ($reason = $this->unavailable($questionnaire)) {
            return $this->unavailableView($questionnaire, $reason);
        }

        return view('questionnaires.public.show', [
            'questionnaire' => $questionnaire,
            'sections' => $questionnaire->sections()->with('questions')->get()->filter(fn ($section) => $section->questions->isNotEmpty()),
            'respondentFields' => $questionnaire->respondentFields(),
            'branding' => $this->branding(),
        ]);
    }

    public function store(SubmitQuestionnaireRequest $request, Questionnaire $questionnaire)
    {
        if ($reason = $this->unavailable($questionnaire)) {
            return $this->unavailableView($questionnaire, $reason);
        }

        // Bots: se descarta en silencio y se muestra el agradecimiento normal.
        if (! $request->isSpam()) {
            $this->responses->store(
                $questionnaire,
                $request->questions(),
                (array) $request->validated('respondent', []),
                (array) $request->validated('answers', [])
            );
        }

        // PRG: recargar la página de gracias no vuelve a enviar.
        return redirect()->route('questionnaires.public.thanks', $questionnaire->token);
    }

    public function thanks(Questionnaire $questionnaire)
    {
        return view('questionnaires.public.thanks', [
            'questionnaire' => $questionnaire,
            'branding' => $this->branding(),
        ]);
    }

    private function unavailable(Questionnaire $questionnaire): ?string
    {
        if ($reason = $questionnaire->unavailableReason()) {
            return $reason;
        }

        // Los instrumentos cerrados se habilitan en los pasos 5 y 6.
        if (! $questionnaire->hasBuilder()) {
            return 'Esta encuesta estará disponible pronto.';
        }

        return $questionnaire->questions()->exists() ? null : 'Esta encuesta aún no tiene preguntas.';
    }

    private function unavailableView(Questionnaire $questionnaire, string $reason)
    {
        return view('questionnaires.public.unavailable', [
            'questionnaire' => $questionnaire,
            'reason' => $reason,
            'branding' => $this->branding(),
        ]);
    }

    private function branding(): array
    {
        $logo = AppSetting::get('app_logo');

        return [
            'name' => AppSetting::get('otec_name'),
            'logo' => $logo ? asset('storage/' . $logo) : null,
        ];
    }
}
