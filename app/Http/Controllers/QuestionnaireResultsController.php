<?php

namespace App\Http\Controllers;

use App\Http\Requests\QuestionnaireResultsFilterRequest;
use App\Models\Questionnaire;
use App\Models\QuestionnaireResponse;
use App\Services\Questionnaires\QuestionnaireResultMailer;
use App\Services\Questionnaires\QuestionnaireResultsService;
use Illuminate\Support\Str;

/**
 * Panel de resultados: resumen, respuestas individuales y exportación CSV.
 */
class QuestionnaireResultsController extends Controller
{
    public function __construct(private QuestionnaireResultsService $results)
    {
    }

    public function summary(QuestionnaireResultsFilterRequest $request, Questionnaire $questionnaire)
    {
        return view('questionnaires.results.summary', $this->viewData($request, $questionnaire, 'summary') + [
            'summary' => $this->results->summary($questionnaire, ...$request->range()),
        ]);
    }

    public function responses(QuestionnaireResultsFilterRequest $request, Questionnaire $questionnaire)
    {
        $responses = $this->results->responsesQuery($questionnaire, ...$request->range())
            ->withCount('answers')
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('questionnaires.results.responses', $this->viewData($request, $questionnaire, 'responses') + [
            'responses' => $responses,
            'instrument' => $questionnaire->instrument(),
        ]);
    }

    public function show(Questionnaire $questionnaire, QuestionnaireResponse $response)
    {
        abort_unless($response->questionnaire_id === $questionnaire->id, 404);

        $response->load('answers');

        // Orden de las preguntas actuales; las eliminadas van al final.
        $order = $questionnaire->orderedQuestions()->pluck('id')->flip();
        $answers = $response->answers->sortBy(fn ($answer) => $order->get($answer->questionnaire_question_id, PHP_INT_MAX))->values();

        return view('questionnaires.results.show', [
            'questionnaire' => $questionnaire,
            'instrument' => $questionnaire->instrument(),
            'response' => $response,
            'answers' => $answers,
            'respondentFields' => array_intersect_key(Questionnaire::RESPONDENT_FIELDS, $questionnaire->respondentFields()),
            'breadcrumbs' => $this->breadcrumbs($questionnaire),
        ]);
    }

    /** Reenvía el resultado de un instrumento (por ejemplo, si el correo falló). */
    public function resend(Questionnaire $questionnaire, QuestionnaireResponse $response, QuestionnaireResultMailer $mailer)
    {
        abort_unless($response->questionnaire_id === $questionnaire->id, 404);
        abort_unless($instrument = $questionnaire->instrument(), 404);

        $sent = $mailer->send($response, $instrument);

        return back()->with(
            $sent ? 'success' : 'error',
            $sent ? "Resultado reenviado a {$response->email}." : 'No se pudo enviar el correo. Revisa la configuración SMTP y storage/logs/laravel.log.'
        );
    }

    public function destroy(Questionnaire $questionnaire, QuestionnaireResponse $response)
    {
        abort_unless($response->questionnaire_id === $questionnaire->id, 404);

        $response->delete();

        return redirect()
            ->route('questionnaires.results.responses', $questionnaire)
            ->with('success', 'Respuesta eliminada.');
    }

    /** Borra todas las respuestas (por ejemplo, las de prueba antes de publicar). El link no cambia. */
    public function destroyAll(Questionnaire $questionnaire)
    {
        $count = $questionnaire->responses()->count();
        $questionnaire->responses()->delete();

        return redirect()
            ->route('questionnaires.results.summary', $questionnaire)
            ->with('success', "Se eliminaron {$count} respuestas. El link y el QR siguen siendo los mismos.");
    }

    /** CSV separado por ";" y con BOM, para que Excel en Chile lo abra bien. */
    public function export(QuestionnaireResultsFilterRequest $request, Questionnaire $questionnaire)
    {
        $export = $this->results->export($questionnaire, ...$request->range());
        $filename = 'resultados-' . Str::slug($questionnaire->name) . '-' . now()->timezone(Questionnaire::TIMEZONE)->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($export) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, $export['headers'], ';', '"', '');

            foreach ($export['rows'] as $row) {
                fputcsv($out, $row, ';', '"', '');
            }

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function viewData(QuestionnaireResultsFilterRequest $request, Questionnaire $questionnaire, string $view): array
    {
        return [
            'questionnaire' => $questionnaire,
            'filters' => $request->filters(),
            'view' => $view,
            'breadcrumbs' => $this->breadcrumbs($questionnaire),
        ];
    }

    private function breadcrumbs(Questionnaire $questionnaire): array
    {
        return [
            ['label' => 'Encuestas', 'url' => route('questionnaires.index')],
            ['label' => $questionnaire->name, 'url' => route('questionnaires.edit', $questionnaire)],
            ['label' => 'Resultados'],
        ];
    }
}
