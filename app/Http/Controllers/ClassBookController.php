<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\Execution;
use App\Services\AttendanceService;
use App\Services\EvaluationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ClassBookController extends Controller
{
    /**
     * Guardar la asistencia de todas las sesiones
     * (matriz participante x sesión).
     */
    public function updateAttendance(
        Request $request,
        Execution $execution,
        AttendanceService $attendanceService
    ) {
        abort_if(
            $execution->isFinalized(),
            403,
            'La ejecución está finalizada. Reábrela para poder editar la asistencia.'
        );

        $validated = $request->validate([
            'attendance' => ['nullable', 'array'],
            'attendance.*' => ['array'],
            'attendance.*.*' => ['integer'],
        ]);

        $attendanceService->saveMatrix(
            $execution,
            $validated['attendance'] ?? []
        );

        return redirect()
            ->route('executions.show', [
                $execution,
                'tab' => 'classbook',
            ])
            ->with(
                'status',
                'Asistencia guardada correctamente.'
            );
    }

    /**
     * Guardar las notas finales de los participantes.
     */
    public function updateGrades(
        Request $request,
        Execution $execution,
        EvaluationService $evaluationService
    ) {
        abort_if(
            $execution->isFinalized(),
            403,
            'La ejecución está finalizada. Reábrela para poder editar las notas.'
        );

        $validated = $request->validate([
            'grades' => ['nullable', 'array'],
            'grades.*' => ['nullable', 'numeric', 'min:1', 'max:7'],
        ]);

        $evaluationService->save(
            $execution,
            $validated['grades'] ?? []
        );

        return redirect()
            ->route('executions.show', [
                $execution,
                'tab' => 'classbook',
            ])
            ->with(
                'status',
                'Notas guardadas correctamente.'
            );
    }

    /**
     * Generar el Libro de Control de Clases en blanco,
     * listo para imprimir y llevar a la clase presencial.
     */
    public function pdf(Execution $execution)
    {
        $execution->load([
            'course',
            'company',
            'sessions',
            'participants.company',
            'instructors',
        ]);

        $otecName = AppSetting::get('otec_name', 'Nombre de la OTEC no configurado');

        $pdf = Pdf::loadView('executions.classbook-pdf', [
            'execution' => $execution,
            'otecName' => $otecName,
        ])->setPaper('letter', 'portrait');

        $filename = 'libro-de-clases-' . $execution->internal_code . '.pdf';

        return $pdf->stream($filename);
    }
}
