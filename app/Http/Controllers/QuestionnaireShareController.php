<?php

namespace App\Http\Controllers;

use App\Models\Questionnaire;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

/**
 * Compartir una encuesta: link, QR (SVG) y hoja para imprimir.
 */
class QuestionnaireShareController extends Controller
{
    public function show(Questionnaire $questionnaire)
    {
        $url = $questionnaire->publicUrl();
        $message = "Te invito a responder la encuesta «{$questionnaire->name}»: {$url}";

        return view('questionnaires.share', [
            'questionnaire' => $questionnaire,
            'url' => $url,
            'qr' => $this->inline($this->qr($url, 240)),
            'whatsappUrl' => 'https://wa.me/?text=' . rawurlencode($message),
            'mailtoUrl' => 'mailto:?subject=' . rawurlencode($questionnaire->name) . '&body=' . rawurlencode($message),
            'responsesCount' => $questionnaire->responses()->count(),
            'lastResponseAt' => $questionnaire->responses()->latest()->value('created_at'),
            'breadcrumbs' => [
                ['label' => 'Encuestas', 'url' => route('questionnaires.index')],
                ['label' => $questionnaire->name],
            ],
        ]);
    }

    public function downloadQr(Questionnaire $questionnaire)
    {
        return response($this->qr($questionnaire->publicUrl(), 600), 200, [
            'Content-Type' => 'image/svg+xml',
            'Content-Disposition' => 'attachment; filename="qr-' . Str::slug($questionnaire->name) . '.svg"',
        ]);
    }

    public function printQr(Questionnaire $questionnaire)
    {
        return view('questionnaires.qr-print', [
            'questionnaire' => $questionnaire,
            'url' => $questionnaire->publicUrl(),
            'qr' => $this->inline($this->qr($questionnaire->publicUrl(), 420)),
        ]);
    }

    /** Sin la declaración XML, para insertarlo dentro del HTML. */
    private function inline(string $svg): string
    {
        return preg_replace('/^<\?xml[^>]*>\s*/', '', $svg);
    }

    private function qr(string $url, int $size): string
    {
        return (string) QrCode::format('svg')->size($size)->margin(1)->errorCorrection('M')->generate($url);
    }
}
