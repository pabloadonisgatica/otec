<?php

namespace App\Services\Questionnaires;

use App\Mail\QuestionnaireResultMail;
use App\Models\QuestionnaireResponse;
use App\Services\Questionnaires\Instruments\Instrument;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

/**
 * Envía el resultado de un instrumento por correo. Si el envío falla,
 * se registra en el log y la persona igual ve su resultado en pantalla.
 */
class QuestionnaireResultMailer
{
    public function send(QuestionnaireResponse $response, Instrument $instrument): bool
    {
        if (! $response->email) {
            return false;
        }

        try {
            Mail::to($response->email, $response->respondentName())
                ->bcc(config('questionnaires.results_bcc', []))
                ->send(new QuestionnaireResultMail($response, $instrument));

            $response->forceFill(['result_emailed_at' => now()])->save();

            return true;
        } catch (Throwable $e) {
            Log::warning('No se pudo enviar el resultado de la encuesta por correo.', [
                'questionnaire_response_id' => $response->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
