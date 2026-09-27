<?php

namespace App\Services;

use App\Models\AppSetting;
use App\Models\Execution;
use App\Models\ExecutionReport;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

/**
 * Arma los datos del Informe de una ejecución.
 *
 * Todo se calcula en vivo desde la ejecución (curso, relatores, sesiones,
 * asistencia, notas y encuesta). Solo orden de compra, factura, invitados,
 * observaciones y sugerencias se ingresan a mano (execution_reports).
 */
class ExecutionReportService
{
    public function build(Execution $execution): array
    {
        $execution->loadMissing([
            'course',
            'company',
            'instructors',
            'participants',
            'sessions.attendances',
            'evaluations',
            'report',
            'executionSurvey.template.sections.fields',
            'executionSurvey.responses.answers',
        ]);

        return [
            'execution'    => $execution,
            'manual'       => $execution->report ?? new ExecutionReport(),
            'otec_name'    => AppSetting::get('otec_name'),
            'logo_path'    => $this->logoPath(),
            'course_name'  => $execution->course_name ?: $execution->course?->name,
            'company_name' => $execution->company?->name,
            'instructors'  => $execution->instructors->pluck('name')->implode(', '),
            'dates'        => $this->datesLabel($execution),
            'place'        => $execution->place,
            'modality'     => $execution->modality,
            'objective'    => $execution->course?->general_objectives,
            'counts'       => $this->counts($execution),
            'survey'       => $this->survey($execution),
        ];
    }

    private function counts(Execution $execution): array
    {
        $enrolled = $execution->participants->count();

        $attendances = $execution->sessions->flatMap->attendances;

        $attended = $attendances->isEmpty()
            ? null
            : $attendances->where('present', true)->pluck('participant_id')->unique()->count();

        $hasGrades = $execution->evaluations->whereNotNull('final_grade')->isNotEmpty();

        return [
            'enrolled' => $enrolled,
            'attended' => $attended,                // null = sin asistencia registrada
            'approved' => $hasGrades                // null = sin notas registradas
                ? $execution->approvedParticipantIds()->count()
                : null,
        ];
    }

    /**
     * "11 de septiembre de 2026", "del 3 al 10 de septiembre de 2026" o
     * "del 28 de agosto al 4 de septiembre de 2026". Usa las sesiones
     * generadas; si no hay, las fechas de la ejecución.
     */
    private function datesLabel(Execution $execution): ?string
    {
        $dates = $execution->sessions
            ->pluck('session_date')
            ->filter()
            ->map(fn ($date) => Carbon::parse($date));

        $start = $dates->min() ?? ($execution->start_date ? Carbon::parse($execution->start_date) : null);
        $end   = $dates->max() ?? ($execution->end_date ? Carbon::parse($execution->end_date) : $start);

        if (! $start) {
            return null;
        }

        if ($start->isSameDay($end)) {
            return $start->translatedFormat('j \d\e F \d\e Y');
        }

        if ($start->isSameMonth($end)) {
            return 'del ' . $start->format('j') . ' al ' . $end->translatedFormat('j \d\e F \d\e Y');
        }

        if ($start->isSameYear($end)) {
            return 'del ' . $start->translatedFormat('j \d\e F') . ' al ' . $end->translatedFormat('j \d\e F \d\e Y');
        }

        return 'del ' . $start->translatedFormat('j \d\e F \d\e Y') . ' al ' . $end->translatedFormat('j \d\e F \d\e Y');
    }

    /**
     * Promedio por pregunta (todas las respuestas, de todos los relatores)
     * y comentarios de las preguntas de texto.
     */
    private function survey(Execution $execution): ?array
    {
        $executionSurvey = $execution->executionSurvey;

        if (! $executionSurvey) {
            return null;
        }

        $answers = $executionSurvey->responses->flatMap->answers
            ->groupBy('survey_template_field_id');

        $number   = 0;
        $sections = [];
        $comments = [];

        foreach ($executionSurvey->template->sections as $section) {
            $questions = [];

            foreach ($section->fields as $field) {
                $values = collect($answers->get($field->id, []))
                    ->pluck('value')
                    ->filter(fn ($value) => $value !== null && trim((string) $value) !== '');

                if (in_array($field->type, ['radio', 'select'], true)) {
                    $questions[] = [
                        'number' => ++$number,
                        'label'  => $field->label,
                        'result' => $this->scoreLabel($values),
                    ];

                    continue;
                }

                if ($values->isNotEmpty()) {
                    $comments[] = [
                        'label' => $field->label,
                        'texts' => $values->values()->all(),
                    ];
                }
            }

            if ($questions) {
                $sections[] = [
                    'title'     => $section->title,
                    'scale'     => $section->description,
                    'questions' => $questions,
                ];
            }
        }

        return [
            'responses' => $executionSurvey->responses->count(),
            'sections'  => $sections,
            'comments'  => $comments,
        ];
    }

    /**
     * Opciones numéricas → promedio con coma decimal ("6,8" o "7").
     * Opciones de texto → la respuesta más frecuente.
     */
    private function scoreLabel(Collection $values): string
    {
        if ($values->isEmpty()) {
            return '—';
        }

        if ($values->every(fn ($value) => is_numeric($value))) {
            $average = round($values->map(fn ($value) => (float) $value)->avg(), 1);

            return str_replace('.', ',', rtrim(rtrim(number_format($average, 1, '.', ''), '0'), '.'));
        }

        return (string) $values->countBy()->sortDesc()->keys()->first();
    }

    private function logoPath(): ?string
    {
        $logo = AppSetting::get('app_logo');

        return $logo && Storage::disk('public')->exists($logo)
            ? Storage::disk('public')->path($logo)
            : null;
    }
}
