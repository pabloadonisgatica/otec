<?php

namespace App\Http\Controllers;

use App\Models\QualityDocument;
use App\Models\QualityDocumentVersion;
use App\Models\QualityStrategicIndicator;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * NCH 2728 — 5.4 Planificación Estratégica.
 *
 * Documento único (singleton, ver QualityDocument::strategicPlan()) con
 * historial de versiones (archivo o enlace externo) e indicadores libres.
 * Sigue el mismo patrón que Manual de Calidad y Requerimientos del Cliente.
 */
class StrategicPlanController extends Controller
{
    public function edit()
    {
        $document = QualityDocument::strategicPlan();
        $document->load(['versions', 'indicators']);

        return view('quality.strategic-plan', compact('document'));
    }

    /**
     * Sube una nueva versión. Las anteriores quedan en el historial.
     */
    public function uploadVersion(Request $request)
    {
        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'document_type' => ['required', 'in:file,link'],
            'file' => ['required_if:document_type,file', 'nullable', 'file', 'max:71680'], // 70MB
            'external_url' => ['required_if:document_type,link', 'nullable', 'url', 'max:255'],
        ]);

        $document = QualityDocument::strategicPlan();

        if (! empty($validated['title'])) {
            $document->update(['name' => $validated['title']]);
        }

        $nextVersion = ($document->versions()->max('version_number') ?? 0) + 1;

        $filePath = null;
        $fileName = null;
        $externalUrl = null;

        if ($validated['document_type'] === 'link') {
            $externalUrl = $validated['external_url'];
        } else {
            $file = $request->file('file');
            $safeBase = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
            $filename = $safeBase . '_v' . $nextVersion . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();

            $filePath = $file->storeAs('quality/planificacion-estrategica', $filename, 'public');
            $fileName = $file->getClientOriginalName();
        }

        QualityDocumentVersion::create([
            'quality_document_id' => $document->id,
            'version_number' => $nextVersion,
            'file_path' => $filePath,
            'file_name' => $fileName,
            'external_url' => $externalUrl,
            'uploaded_by' => auth()->user()->name ?? 'Sistema',
            'uploaded_at' => now(),
        ]);

        return redirect()
            ->route('quality.strategic-plan')
            ->with('status', "Versión {$nextVersion} subida correctamente.");
    }

    public function storeIndicator(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'value' => ['required', 'string', 'max:255'],
        ]);

        QualityDocument::strategicPlan()->indicators()->create($validated);

        return redirect()
            ->route('quality.strategic-plan')
            ->with('status', 'Indicador agregado.');
    }

    public function updateIndicator(Request $request, QualityStrategicIndicator $indicator)
    {
        $validated = $request->validate([
            'value' => ['required', 'string', 'max:255'],
        ]);

        $indicator->update($validated);

        return redirect()
            ->route('quality.strategic-plan')
            ->with('status', 'Indicador actualizado.');
    }

    public function destroyIndicator(QualityStrategicIndicator $indicator)
    {
        $indicator->delete();

        return redirect()
            ->route('quality.strategic-plan')
            ->with('status', 'Indicador eliminado.');
    }
}
