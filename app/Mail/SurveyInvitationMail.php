<?php

namespace App\Mail;

use App\Models\SurveyResponse;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SurveyInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public SurveyResponse $response
    ) {}

    public function build()
    {
        $execution = $this->response->execution;
        $participant = $this->response->participant;

        return $this
            ->subject('Tu opinión es importante: encuesta de "' . $execution->course_name . '"')
            ->view('emails.survey-invitation', [
                'participant' => $participant,
                'execution' => $execution,
                'url' => route('survey.show', $this->response->token),
            ]);
    }
}
