<?php

namespace App\Http\Controllers;

use App\Models\Execution;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Excel de la encuesta de satisfacción de una ejecución:
 * una fila por respuesta y relator evaluado, una columna por pregunta.
 */
class ExecutionSurveyExportController extends Controller
{
    public function export(Execution $execution)
    {
        $executionSurvey = $execution->executionSurvey;

        abort_unless($executionSurvey, 404);

        $executionSurvey->loadMissing([
            'template.sections.fields',
            'execution.instructors',
            'responses.answers',
        ]);

        $sections = $executionSurvey->template->sections;
        $instructorNames = $executionSurvey->execution->instructors->pluck('name', 'id');

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Respuestas');

        $set = function (int $col, int $row, $value) use ($sheet) {
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($col) . $row, $value);
        };

        // Encabezados
        $col = 1;
        $set($col++, 1, '#');
        $set($col++, 1, 'Fecha');
        $set($col++, 1, 'Relator');

        foreach ($sections as $section) {
            foreach ($section->fields as $field) {
                $set($col++, 1, $section->title ? "{$section->title} - {$field->label}" : $field->label);
            }
        }

        // Filas. Las respuestas antiguas (de antes de elegir relator) pueden
        // traer varios relatores: se exporta una fila por cada uno.
        $row = 2;

        foreach ($executionSurvey->responses as $i => $response) {
            foreach ($response->answers->groupBy('instructor_id') as $instructorId => $answers) {
                $byField = $answers->keyBy('survey_template_field_id');

                $col = 1;
                $set($col++, $row, $i + 1);
                $set($col++, $row, $response->submitted_at?->format('d-m-Y H:i'));
                $set($col++, $row, $instructorNames->get($instructorId) ?? ($instructorId ? "Relator #{$instructorId}" : '—'));

                foreach ($sections as $section) {
                    foreach ($section->fields as $field) {
                        $set($col++, $row, $byField->get($field->id)?->value ?? '');
                    }
                }

                $row++;
            }
        }

        $filename = 'encuesta-' . $execution->internal_code . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
