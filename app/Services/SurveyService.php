<?php

namespace App\Services;

use App\Mail\SurveyInvitationMail;
use App\Mail\SurveyThankYouMail;
use App\Models\Execution;
use App\Models\SurveyQuestion;
use App\Models\SurveyResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class SurveyService
{
    /**
     * Crea (si no existen) las respuestas pendientes de cada
     * participante y envía el correo con su link personalizado.
     *
     * No reenvía a quien ya recibió invitación, salvo que
     * $resend sea true.
     */
    public function sendInvitations(Execution $execution, bool $resend = false): int
    {
        $sent = 0;

        foreach ($execution->participants as $participant) {

            if (! $participant->email) {
                continue;
            }

            $response = SurveyResponse::firstOrCreate(
                [
                    'execution_id' => $execution->id,
                    'participant_id' => $participant->id,
                ],
                [
                    'token' => Str::random(48),
                ]
            );

            if ($response->isSubmitted()) {
                continue;
            }

            if ($response->invited_at && ! $resend) {
                continue;
            }

            Mail::to($participant->email)->send(
                new SurveyInvitationMail($response)
            );

            $response->update(['invited_at' => now()]);

            $sent++;
        }

        return $sent;
    }

    /**
     * Guarda las respuestas de la encuesta y notifica
     * al participante por correo.
     *
     * $scores = [survey_question_id => score|null, ...]
     */
    public function submit(
        SurveyResponse $response,
        array $scores,
        ?string $suggestions
    ): void {

        foreach (SurveyQuestion::all() as $question) {

            $response->answers()->updateOrCreate(
                ['survey_question_id' => $question->id],
                ['score' => $scores[$question->id] ?? null]
            );
        }

        $response->update([
            'suggestions' => $suggestions,
            'submitted_at' => now(),
        ]);

        if ($response->participant->email) {
            Mail::to($response->participant->email)->send(
                new SurveyThankYouMail($response)
            );
        }
    }

    /**
     * Resumen de resultados de una ejecución: promedio
     * por pregunta y totales de respuestas.
     */
    public function summary(Execution $execution): array
    {
        $questions = SurveyQuestion::orderBy('sort_order')->get();

        $responses = $execution->surveyResponses()
            ->with('answers')
            ->get();

        $submitted = $responses->filter->isSubmitted();

        $questionAverages = $questions->map(function ($question) use ($submitted) {

            $scores = $submitted
                ->flatMap->answers
                ->where('survey_question_id', $question->id)
                ->pluck('score')
                ->filter(fn ($score) => $score !== null);

            return [
                'question' => $question,
                'average' => $scores->isNotEmpty()
                    ? round($scores->avg(), 1)
                    : null,
                'answers_count' => $scores->count(),
            ];
        });

        return [
            'total_participants' => $execution->participants->count(),
            'invited_count' => $responses->whereNotNull('invited_at')->count(),
            'submitted_count' => $submitted->count(),
            'question_averages' => $questionAverages,
            'suggestions' => $submitted
                ->pluck('suggestions')
                ->filter()
                ->values(),
        ];
    }
}
