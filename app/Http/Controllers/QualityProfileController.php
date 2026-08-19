<?php

namespace App\Http\Controllers;

use App\Models\QualityProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class QualityProfileController extends Controller
{
    public function edit()
    {
        $profile = QualityProfile::current();

        return view('quality.profile', compact('profile'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'vision' => ['nullable', 'string'],
            'mission' => ['nullable', 'string'],
            'scope' => ['nullable', 'string'],
            'last_audit_date' => ['nullable', 'date'],
        ]);

        QualityProfile::current()->update($validated);

        return redirect()
            ->route('quality.profile.edit', ['tab' => 'general'])
            ->with('status', 'Requisitos generales actualizados correctamente.');
    }

    /**
     * Subir / reemplazar el Mapa de Procesos.
     */
    public function uploadProcessMap(Request $request)
    {
        $this->uploadSingleImage($request, column: 'process_map');

        return redirect()
            ->route('quality.profile.edit', ['tab' => 'process-map'])
            ->with('status', 'Mapa de Procesos actualizado correctamente.');
    }

    /**
     * Subir / reemplazar el Organigrama.
     */
    public function uploadOrgChart(Request $request)
    {
        $this->uploadSingleImage($request, column: 'org_chart');

        return redirect()
            ->route('quality.profile.edit', ['tab' => 'org-chart'])
            ->with('status', 'Organigrama actualizado correctamente.');
    }

    /**
     * Lógica compartida: valida título + imagen, borra la
     * imagen anterior si existía, y guarda la nueva.
     */
    private function uploadSingleImage(Request $request, string $column): void
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'file' => ['required', 'image', 'mimes:png,jpg,jpeg', 'max:5120'], // 5MB
        ]);

        $profile = QualityProfile::current();

        $pathColumn = $column . '_path';
        $titleColumn = $column . '_title';

        if ($profile->$pathColumn && Storage::disk('public')->exists($profile->$pathColumn)) {
            Storage::disk('public')->delete($profile->$pathColumn);
        }

        $path = $request->file('file')->store('quality/' . $column, 'public');

        $profile->update([
            $titleColumn => $validated['title'],
            $pathColumn => $path,
        ]);
    }

    // ---------------------------------------------------------------
    // Documentación Legal
    // ---------------------------------------------------------------

    public function uploadDocument(Request $request)
    {
        $this->uploadToDocumentList($request, 'legal_documents', 'quality/legal-documents');

        return redirect()
            ->route('quality.profile.edit', ['tab' => 'legal'])
            ->with('status', 'Documento agregado correctamente.');
    }

    public function downloadDocument(int $index)
    {
        return $this->downloadFromDocumentList('legal_documents', $index);
    }

    public function deleteDocument(int $index)
    {
        $this->deleteFromDocumentList('legal_documents', $index);

        return redirect()
            ->route('quality.profile.edit', ['tab' => 'legal'])
            ->with('status', 'Documento eliminado.');
    }

    // ---------------------------------------------------------------
    // Requisitos Financieros (6.5)
    // ---------------------------------------------------------------

    public function financial()
    {
        $profile = QualityProfile::current();

        return view('quality.financial-documents', compact('profile'));
    }

    public function uploadFinancialDocument(Request $request)
    {
        $this->uploadToDocumentList($request, 'financial_documents', 'quality/financial-documents');

        return redirect()
            ->route('quality.financial-documents')
            ->with('status', 'Documento agregado correctamente.');
    }

    public function downloadFinancialDocument(int $index)
    {
        return $this->downloadFromDocumentList('financial_documents', $index);
    }

    public function deleteFinancialDocument(int $index)
    {
        $this->deleteFromDocumentList('financial_documents', $index);

        return redirect()
            ->route('quality.financial-documents')
            ->with('status', 'Documento eliminado.');
    }

    // ---------------------------------------------------------------
    // Lógica compartida: listas de documentos (array en QualityProfile)
    // ---------------------------------------------------------------

    private function uploadToDocumentList(Request $request, string $field, string $storagePath): void
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'file' => ['required', 'file', 'max:71680'], // 70MB
        ]);

        $profile = QualityProfile::current();
        $docs = $profile->$field ?? [];

        $file = $request->file('file');

        $safeBase = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $ext = $file->getClientOriginalExtension();
        $filename = $safeBase . '_' . Str::random(8) . '.' . $ext;

        $path = $file->storeAs($storagePath, $filename, 'public');

        $docs[] = [
            'title' => $validated['title'],
            'path' => $path,
            'name' => $file->getClientOriginalName(),
            'mime' => $file->getClientMimeType(),
            'size' => $file->getSize(),
            'uploaded_by' => auth()->user()->name ?? 'Sistema',
            'uploaded_at' => now()->toDateTimeString(),
        ];

        $profile->$field = $docs;
        $profile->save();
    }

    private function downloadFromDocumentList(string $field, int $index)
    {
        $profile = QualityProfile::current();
        $docs = $profile->$field ?? [];

        abort_unless(isset($docs[$index]), 404);

        $doc = $docs[$index];

        abort_unless(Storage::disk('public')->exists($doc['path']), 404);

        return Storage::disk('public')->download($doc['path'], $doc['name'] ?? 'documento');
    }

    private function deleteFromDocumentList(string $field, int $index): void
    {
        $profile = QualityProfile::current();
        $docs = $profile->$field ?? [];

        abort_unless(isset($docs[$index]), 404);

        $doc = $docs[$index];

        if (!empty($doc['path']) && Storage::disk('public')->exists($doc['path'])) {
            Storage::disk('public')->delete($doc['path']);
        }

        array_splice($docs, $index, 1);
        $profile->$field = $docs;
        $profile->save();
    }
}
