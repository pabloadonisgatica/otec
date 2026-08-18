<?php

namespace App\Http\Controllers;

use App\Models\QualityDocument;
use App\Models\QualityDocumentVersion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ExternalDocumentController extends Controller
{
    public function index()
    {
        $documents = QualityDocument::where('type', 'documento_externo')
            ->with('versions')
            ->orderBy('name')
            ->paginate(20);

        return view('quality.external-documents.index', compact('documents'));
    }

    public function create()
    {
        return view('quality.external-documents.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateDocument($request);

        $document = QualityDocument::create([
            'type' => 'documento_externo',
            'name' => $validated['name'],
            'next_review_date' => $validated['next_review_date'] ?? null,
        ]);

        $this->storeVersion($request, $document, 1);

        return redirect()
            ->route('quality.external-documents.show', $document)
            ->with('status', 'Documento externo creado correctamente.');
    }

    public function show(QualityDocument $externalDocument)
    {
        abort_unless($externalDocument->type === 'documento_externo', 404);

        $externalDocument->load('versions');

        return view('quality.external-documents.show', ['document' => $externalDocument]);
    }

    public function edit(QualityDocument $externalDocument)
    {
        abort_unless($externalDocument->type === 'documento_externo', 404);

        return view('quality.external-documents.edit', ['document' => $externalDocument]);
    }

    public function update(Request $request, QualityDocument $externalDocument)
    {
        abort_unless($externalDocument->type === 'documento_externo', 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'next_review_date' => ['nullable', 'date'],
        ]);

        $externalDocument->update($validated);

        return redirect()
            ->route('quality.external-documents.show', $externalDocument)
            ->with('status', 'Documento externo actualizado correctamente.');
    }

    public function destroy(QualityDocument $externalDocument)
    {
        abort_unless($externalDocument->type === 'documento_externo', 404);

        foreach ($externalDocument->versions as $version) {
            if ($version->file_path) {
                Storage::disk('public')->delete($version->file_path);
            }
        }

        $externalDocument->delete();

        return redirect()
            ->route('quality.external-documents.index')
            ->with('status', 'Documento externo eliminado.');
    }

    public function uploadVersion(Request $request, QualityDocument $externalDocument)
    {
        abort_unless($externalDocument->type === 'documento_externo', 404);

        $request->validate([
            'document_type' => ['required', 'in:file,link'],
            'file' => ['required_if:document_type,file', 'nullable', 'file', 'max:71680'],
            'external_url' => ['required_if:document_type,link', 'nullable', 'url', 'max:255'],
        ]);

        $nextVersion = ($externalDocument->versions()->max('version_number') ?? 0) + 1;

        $this->storeVersion($request, $externalDocument, $nextVersion);

        return redirect()
            ->route('quality.external-documents.show', $externalDocument)
            ->with('status', "Versión {$nextVersion} subida correctamente.");
    }

    private function validateDocument(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'document_type' => ['required', 'in:file,link'],
            'file' => ['required_if:document_type,file', 'nullable', 'file', 'max:71680'],
            'external_url' => ['required_if:document_type,link', 'nullable', 'url', 'max:255'],
            'next_review_date' => ['nullable', 'date'],
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

            $filePath = $file->storeAs('quality/documentos-externos', $filename, 'public');
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
