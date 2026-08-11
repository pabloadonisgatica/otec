<?php

namespace App\Mail;

use App\Models\SurveyResponse;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SurveyThankYouMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public SurveyResponse $response
    ) {}

    public function build()
    {
        $this->response->loadMissing('answers.question');

        return $this
            ->subject('Gracias por tu respuesta — ' . $this->response->execution->course_name)
            ->view('emails.survey-thank-you', [
                'participant' => $this->response->participant,
                'execution' => $this->response->execution,
                'answers' => $this->response->answers,
                'suggestions' => $this->response->suggestions,
            ]);
    }
}
