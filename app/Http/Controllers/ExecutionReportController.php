<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateExecutionReportRequest;
use App\Models\Execution;
use App\Services\ExecutionReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class ExecutionReportController extends Controller
{
    /**
     * Guarda los datos manuales del informe.
     */
    public function update(UpdateExecutionReportRequest $request, Execution $execution)
    {
        $execution->report()->updateOrCreate([], $request->validated());

        return redirect()
            ->route('executions.show', [$execution, 'tab' => 'report'])
            ->with('status', 'Datos del informe guardados.');
    }

    /**
     * Descarga el informe en PDF.
     */
    public function pdf(Execution $execution, ExecutionReportService $service)
    {
        $data = $service->build($execution);

        $filename = 'informe-' . Str::slug($data['course_name'] ?? $execution->internal_code)
            . '-' . $execution->internal_code . '.pdf';

        return Pdf::loadView('executions.report-pdf', $data)
            ->setPaper('letter', 'portrait')
            ->download($filename);
    }
}
