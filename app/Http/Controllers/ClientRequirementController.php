<?php

namespace App\Http\Controllers;

use App\Models\QualityDocument;
use App\Models\QualityDocumentVersion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ClientRequirementController extends Controller
{
    public function index()
    {
        $requirements = QualityDocument::where('type', 'requerimiento_cliente')
            ->with('versions')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('quality.requirements.index', compact('requirements'));
    }

    public function create()
    {
        return view('quality.requirements.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateDocument($request);

        $requirement = QualityDocument::create([
            'type' => 'requerimiento_cliente',
            'name' => $validated['name'],
        ]);

        $this->storeVersion($request, $requirement, 1);

        return redirect()
            ->route('quality.requirements.show', $requirement)
            ->with('status', 'Requerimiento del cliente creado correctamente.');
    }

    public function show(QualityDocument $requirement)
    {
        abort_unless($requirement->type === 'requerimiento_cliente', 404);

        $requirement->load('versions');

        return view('quality.requirements.show', compact('requirement'));
    }

    public function edit(QualityDocument $requirement)
    {
        abort_unless($requirement->type === 'requerimiento_cliente', 404);

        return view('quality.requirements.edit', compact('requirement'));
    }

    public function update(Request $request, QualityDocument $requirement)
    {
        abort_unless($requirement->type === 'requerimiento_cliente', 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $requirement->update($validated);

        return redirect()
            ->route('quality.requirements.show', $requirement)
            ->with('status', 'Requerimiento actualizado correctamente.');
    }

    public function destroy(QualityDocument $requirement)
    {
        abort_unless($requirement->type === 'requerimiento_cliente', 404);

        foreach ($requirement->versions as $version) {
            if ($version->file_path) {
                Storage::disk('public')->delete($version->file_path);
            }
        }

        $requirement->delete();

        return redirect()
            ->route('quality.requirements.index')
            ->with('status', 'Requerimiento eliminado.');
    }

    public function uploadVersion(Request $request, QualityDocument $requirement)
    {
        abort_unless($requirement->type === 'requerimiento_cliente', 404);

        $request->validate([
            'document_type' => ['required', 'in:file,link'],
            'file' => ['required_if:document_type,file', 'nullable', 'file', 'max:71680'],
            'external_url' => ['required_if:document_type,link', 'nullable', 'url', 'max:255'],
        ]);

        $nextVersion = ($requirement->versions()->max('version_number') ?? 0) + 1;

        $this->storeVersion($request, $requirement, $nextVersion);

        return redirect()
            ->route('quality.requirements.show', $requirement)
            ->with('status', "Versión {$nextVersion} subida correctamente.");
    }

    private function validateDocument(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'document_type' => ['required', 'in:file,link'],
            'file' => ['required_if:document_type,file', 'nullable', 'file', 'max:71680'],
            'external_url' => ['required_if:document_type,link', 'nullable', 'url', 'max:255'],
        ]);
    }

    private function storeVersion(Request $request, QualityDocument $document, int $versionNumber): void
    {
        $filePath = null;
        $fileName = null;
        $externalUrl = null;

        if ($request->input('document_type') === 'link') {
            $externalUrl = $request->input('external_url');
        } else {
            $file = $request->file('file');
            $safeBase = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
            $ext = $file->getClientOriginalExtension();
            $filename = $safeBase . '_v' . $versionNumber . '_' . Str::random(6) . '.' . $ext;

            $filePath = $file->storeAs('quality/requerimientos', $filename, 'public');
            $fileName = $file->getClientOriginalName();
        }

        QualityDocumentVersion::create([
            'quality_document_id' => $document->id,
            'version_number' => $versionNumber,
            'file_path' => $filePath,
            'file_name' => $fileName,
            'external_url' => $externalUrl,
            'uploaded_by' => auth()->user()->name ?? 'Sistema',
            'uploaded_at' => now(),
        ]);
    }
}
