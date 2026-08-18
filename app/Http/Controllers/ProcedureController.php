<?php

namespace App\Http\Controllers;

use App\Models\QualityDocument;
use App\Models\QualityDocumentVersion;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProcedureController extends Controller
{
    public function index()
    {
        $procedures = QualityDocument::where('type', 'procedimiento')
            ->with('versions')
            ->orderBy('name')
            ->paginate(20);

        return view('quality.procedures.index', compact('procedures'));
    }

    public function create()
    {
        return view('quality.procedures.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateDocument($request);

        $procedure = QualityDocument::create([
            'type' => 'procedimiento',
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'reviewer_name' => $validated['reviewer_name'] ?? null,
            'review_date' => $validated['review_date'] ?? null,
            'approver_name' => $validated['approver_name'] ?? null,
            'approval_date' => $validated['approval_date'] ?? null,
            'next_review_date' => $validated['next_review_date'] ?? null,
        ]);

        $this->storeVersion($request, $procedure, 1, $validated['observation'] ?? null);

        return redirect()
            ->route('quality.procedures.show', $procedure)
            ->with('status', 'Procedimiento creado correctamente.');
    }

    public function show(QualityDocument $procedure)
    {
        abort_unless($procedure->type === 'procedimiento', 404);

        $procedure->load('versions');

        return view('quality.procedures.show', compact('procedure'));
    }

    public function edit(QualityDocument $procedure)
    {
        abort_unless($procedure->type === 'procedimiento', 404);

        return view('quality.procedures.edit', compact('procedure'));
    }

    public function update(Request $request, QualityDocument $procedure)
    {
        abort_unless($procedure->type === 'procedimiento', 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'reviewer_name' => ['nullable', 'string', 'max:255'],
            'review_date' => ['nullable', 'date'],
            'approver_name' => ['nullable', 'string', 'max:255'],
            'approval_date' => ['nullable', 'date'],
            'next_review_date' => ['nullable', 'date'],
        ]);

        $procedure->update($validated);

        return redirect()
            ->route('quality.procedures.show', $procedure)
            ->with('status', 'Procedimiento actualizado correctamente.');
    }

    public function destroy(QualityDocument $procedure)
    {
        abort_unless($procedure->type === 'procedimiento', 404);

        foreach ($procedure->versions as $version) {
            if ($version->file_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($version->file_path);
            }
        }

        $procedure->delete();

        return redirect()
            ->route('quality.procedures.index')
            ->with('status', 'Procedimiento eliminado.');
    }

    /**
     * Subir una nueva versión de un procedimiento existente.
     */
    public function uploadVersion(Request $request, QualityDocument $procedure)
    {
        abort_unless($procedure->type === 'procedimiento', 404);

        $validated = $request->validate([
            'document_type' => ['required', 'in:file,link'],
            'file' => ['required_if:document_type,file', 'nullable', 'file', 'max:71680'],
            'external_url' => ['required_if:document_type,link', 'nullable', 'url', 'max:255'],
            'observation' => ['nullable', 'string'],
        ]);

        $nextVersion = ($procedure->versions()->max('version_number') ?? 0) + 1;

        $this->storeVersion($request, $procedure, $nextVersion, $validated['observation'] ?? null);

        return redirect()
            ->route('quality.procedures.show', $procedure)
            ->with('status', "Versión {$nextVersion} subida correctamente.");
    }

    private function validateDocument(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'document_type' => ['required', 'in:file,link'],
            'file' => ['required_if:document_type,file', 'nullable', 'file', 'max:71680'], // 70MB
            'external_url' => ['required_if:document_type,link', 'nullable', 'url', 'max:255'],
            'reviewer_name' => ['nullable', 'string', 'max:255'],
            'review_date' => ['nullable', 'date'],
            'approver_name' => ['nullable', 'string', 'max:255'],
            'approval_date' => ['nullable', 'date'],
            'next_review_date' => ['nullable', 'date'],
            'observation' => ['nullable', 'string'],
        ]);
    }

    private function storeVersion(
        Request $request,
        QualityDocument $procedure,
        int $versionNumber,
        ?string $observation
    ): void {
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

            $filePath = $file->storeAs('quality/procedimientos', $filename, 'public');
            $fileName = $file->getClientOriginalName();
        }

        QualityDocumentVersion::create([
            'quality_document_id' => $procedure->id,
            'version_number' => $versionNumber,
            'file_path' => $filePath,
            'file_name' => $fileName,
            'external_url' => $externalUrl,
            'observation' => $observation,
            'uploaded_by' => auth()->user()->name ?? 'Sistema',
            'uploaded_at' => now(),
        ]);
    }
}
