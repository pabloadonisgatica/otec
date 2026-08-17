<?php

namespace App\Http\Controllers;

use App\Models\QualityDocument;
use App\Models\QualityDocumentVersion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class QualityDocumentController extends Controller
{
    public function manualCalidad()
    {
        $document = QualityDocument::manualCalidad();
        $document->load('versions');

        return view('quality.manual-calidad', compact('document'));
    }

    /**
     * Guardar los metadatos de control (encargados, fechas).
     */
    public function updateManualCalidad(Request $request)
    {
        $validated = $request->validate([
            'reviewer_name' => ['nullable', 'string', 'max:255'],
            'review_date' => ['nullable', 'date'],
            'approver_name' => ['nullable', 'string', 'max:255'],
            'approval_date' => ['nullable', 'date'],
            'next_review_date' => ['nullable', 'date'],
        ]);

        QualityDocument::manualCalidad()->update($validated);

        return redirect()
            ->route('quality.manual-calidad')
            ->with('status', 'Datos de control actualizados correctamente.');
    }

    /**
     * Subir una nueva versión. La anterior NO se borra —
     * queda en el historial (a diferencia de Mapa de Procesos
     * y Organigrama, que sí reemplazan el archivo).
     */
    public function uploadVersion(Request $request)
    {
        $validated = $request->validate([
            'file' => ['required', 'file', 'max:71680'], // 70MB
            'observation' => ['nullable', 'string'],
        ]);

        $document = QualityDocument::manualCalidad();

        $nextVersion = ($document->versions()->max('version_number') ?? 0) + 1;

        $file = $request->file('file');

        $safeBase = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $ext = $file->getClientOriginalExtension();
        $filename = $safeBase . '_v' . $nextVersion . '_' . Str::random(6) . '.' . $ext;

        $path = $file->storeAs('quality/manual-calidad', $filename, 'public');

        QualityDocumentVersion::create([
            'quality_document_id' => $document->id,
            'version_number' => $nextVersion,
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'observation' => $validated['observation'] ?? null,
            'uploaded_by' => auth()->user()->name ?? 'Sistema',
            'uploaded_at' => now(),
        ]);

        return redirect()
            ->route('quality.manual-calidad')
            ->with('status', "Versión {$nextVersion} subida correctamente.");
    }

    /**
     * Descargar una versión específica (vigente o del historial).
     */
    public function downloadVersion(QualityDocumentVersion $version)
    {
        abort_unless(Storage::disk('public')->exists($version->file_path), 404);

        return Storage::disk('public')->download($version->file_path, $version->file_name);
    }
}
