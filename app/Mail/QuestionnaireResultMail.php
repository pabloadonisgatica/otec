<?php

namespace App\Mail;

use App\Models\AppSetting;
use App\Models\QuestionnaireResponse;
use App\Services\Questionnaires\Instruments\Instrument;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Resultado de un instrumento cerrado, enviado a quien respondió.
 * Se envía al instante (sin cola): el hosting no corre un worker.
 */
class QuestionnaireResultMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public QuestionnaireResponse $response,
        public Instrument $instrument
    ) {}

    public function build()
    {
        return $this
            ->subject('Tus resultados: ' . $this->instrument->title())
            ->view('emails.questionnaires.result', [
                'response' => $this->response,
                'instrument' => $this->instrument,
                'otecName' => AppSetting::get('otec_name'),
            ])
            ->text('emails.questionnaires.result-text', [
                'response' => $this->response,
                'instrument' => $this->instrument,
                'otecName' => AppSetting::get('otec_name'),
            ]);
    }
}
