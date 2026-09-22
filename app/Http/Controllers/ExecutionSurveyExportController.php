<?php

namespace App\Http\Controllers;

use App\Models\Execution;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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

        $generalSections = $executionSurvey->template->sections->where('repeats_per_instructor', false)->values();
        $repeatingSections = $executionSurvey->template->sections->where('repeats_per_instructor', true)->values();
        $instructors = $executionSurvey->execution->instructors;

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

        foreach ($generalSections as $section) {
            foreach ($section->fields as $field) {
                $set($col++, 1, $field->label);
            }
        }

        foreach ($instructors as $instructor) {
            foreach ($repeatingSections as $section) {
                foreach ($section->fields as $field) {
                    $set($col++, 1, "{$instructor->name} - {$field->label}");
                }
            }
        }

        // Filas
        $row = 2;
        foreach ($executionSurvey->responses as $i => $response) {
            $col = 1;
            $set($col++, $row, $i + 1);
            $set($col++, $row, $response->submitted_at?->format('d-m-Y H:i'));

            foreach ($generalSections as $section) {
                foreach ($section->fields as $field) {
                    $answer = $response->answers->first(
                        fn ($a) => $a->survey_template_field_id === $field->id && $a->instructor_id === null
                    );
                    $set($col++, $row, $answer->value ?? '');
                }
            }

            foreach ($instructors as $instructor) {
                foreach ($repeatingSections as $section) {
                    foreach ($section->fields as $field) {
                        $answer = $response->answers->first(
                            fn ($a) => $a->survey_template_field_id === $field->id && $a->instructor_id === $instructor->id
                        );
                        $set($col++, $row, $answer->value ?? '');
                    }
                }
            }

            $row++;
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
