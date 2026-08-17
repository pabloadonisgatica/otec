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
        $this->uploadSingleImage(
            $request,
            column: 'process_map',
        );

        return redirect()
            ->route('quality.profile.edit', ['tab' => 'process-map'])
            ->with('status', 'Mapa de Procesos actualizado correctamente.');
    }

    /**
     * Subir / reemplazar el Organigrama.
     */
    public function uploadOrgChart(Request $request)
    {
        $this->uploadSingleImage(
            $request,
            column: 'org_chart',
        );

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

    /**
     * Subir un documento legal.
     */
    public function uploadDocument(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'file' => ['required', 'file', 'max:71680'], // 70MB, igual al sistema de referencia
        ]);

        $profile = QualityProfile::current();
        $docs = $profile->legal_documents ?? [];

        $file = $validated['file'] ?? $request->file('file');

        $safeBase = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $ext = $file->getClientOriginalExtension();
        $filename = $safeBase . '_' . Str::random(8) . '.' . $ext;

        $path = $file->storeAs('quality/legal-documents', $filename, 'public');

        $docs[] = [
            'title' => $validated['title'],
            'path' => $path,
            'name' => $file->getClientOriginalName(),
            'mime' => $file->getClientMimeType(),
            'size' => $file->getSize(),
            'uploaded_by' => auth()->user()->name ?? 'Sistema',
            'uploaded_at' => now()->toDateTimeString(),
        ];

        $profile->legal_documents = $docs;
        $profile->save();

        return redirect()
            ->route('quality.profile.edit', ['tab' => 'legal'])
            ->with('status', 'Documento agregado correctamente.');
    }

    /**
     * Descargar / ver un documento legal.
     */
    public function downloadDocument(int $index)
    {
        $profile = QualityProfile::current();
        $docs = $profile->legal_documents ?? [];

        abort_unless(isset($docs[$index]), 404);

        $doc = $docs[$index];

        abort_unless(Storage::disk('public')->exists($doc['path']), 404);

        return Storage::disk('public')->download($doc['path'], $doc['name'] ?? 'documento');
    }

    /**
     * Eliminar un documento legal.
     */
    public function deleteDocument(int $index)
    {
        $profile = QualityProfile::current();
        $docs = $profile->legal_documents ?? [];

        abort_unless(isset($docs[$index]), 404);

        $doc = $docs[$index];

        if (!empty($doc['path']) && Storage::disk('public')->exists($doc['path'])) {
            Storage::disk('public')->delete($doc['path']);
        }

        array_splice($docs, $index, 1);
        $profile->legal_documents = $docs;
        $profile->save();

        return redirect()
            ->route('quality.profile.edit', ['tab' => 'legal'])
            ->with('status', 'Documento eliminado.');
    }
}
