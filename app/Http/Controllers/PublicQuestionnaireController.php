<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubmitQuestionnaireRequest;
use App\Models\AppSetting;
use App\Models\Questionnaire;
use App\Models\QuestionnaireResponse;
use App\Services\Questionnaires\QuestionnaireResponseService;
use App\Services\Questionnaires\QuestionnaireResultMailer;

/**
 * Página pública de una encuesta (sin login, identificada por token).
 */
class PublicQuestionnaireController extends Controller
{
    /** Sesión: respuesta recién enviada por token, para mostrar su resultado (PRG). */
    private const RESULT_SESSION = 'questionnaire_results';

    public function __construct(
        private QuestionnaireResponseService $responses,
        private QuestionnaireResultMailer $mailer
    ) {
    }

    public function show(Questionnaire $questionnaire)
    {
        if ($reason = $this->unavailable($questionnaire)) {
            return $this->unavailableView($questionnaire, $reason);
        }

        if ($instrument = $questionnaire->instrument()) {
            return view('questionnaires.instruments.' . $instrument->key() . '.form', [
                'questionnaire' => $questionnaire,
                'instrument' => $instrument,
                'respondentFields' => $questionnaire->respondentFields(),
                'branding' => $this->branding(),
            ]);
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
        if ($request->isSpam()) {
            return redirect()->route('questionnaires.public.thanks', $questionnaire->token);
        }

        if ($instrument = $questionnaire->instrument()) {
            $response = $this->responses->storeInstrument(
                $questionnaire,
                $instrument,
                (array) $request->validated('respondent', []),
                (array) $request->validated('answers', [])
            );

            $emailed = $this->mailer->send($response, $instrument);

            session()->put(self::RESULT_SESSION . '.' . $questionnaire->token, [
                'response_id' => $response->id,
                'emailed' => $emailed,
            ]);

            return redirect()->route('questionnaires.public.result', $questionnaire->token);
        }

        $this->responses->store(
            $questionnaire,
            $request->questions(),
            (array) $request->validated('respondent', []),
            (array) $request->validated('answers', [])
        );

        // PRG: recargar la página de gracias no vuelve a enviar.
        return redirect()->route('questionnaires.public.thanks', $questionnaire->token);
    }

    /** Resultado de un instrumento, solo para quien acaba de responder (sesión). */
    public function result(Questionnaire $questionnaire)
    {
        $instrument = $questionnaire->instrument();
        $state = session(self::RESULT_SESSION . '.' . $questionnaire->token);

        $response = $instrument && $state
            ? QuestionnaireResponse::where('questionnaire_id', $questionnaire->id)->find($state['response_id'])
            : null;

        if (! $response) {
            return redirect()->route('questionnaires.public.show', $questionnaire->token);
        }

        return view('questionnaires.public.result', [
            'questionnaire' => $questionnaire,
            'instrument' => $instrument,
            'response' => $response,
            'emailed' => $state['emailed'],
            'branding' => $this->branding(),
        ]);
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

        if ($questionnaire->instrument()) {
            return null;
        }

        // Instrumento cerrado aún sin implementar.
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
