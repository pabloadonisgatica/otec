<?php

namespace App\Http\Controllers;

use App\Models\Instructor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class InstructorController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        $instructors = Instructor::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('rut', 'like', "%{$q}%")
                        ->orWhere('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%")
                        ->orWhere('phone', 'like', "%{$q}%")
                        ->orWhere('profession', 'like', "%{$q}%");
                });
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $breadcrumbs = [
            ['label' => 'Relatores'],
        ];

        return view('instructors.index', compact('instructors', 'q', 'breadcrumbs'));
    }

    public function create()
    {
        $breadcrumbs = [
            ['label' => 'Relatores', 'url' => route('instructors.index')],
            ['label' => 'Nuevo relator'],
        ];

        return view('instructors.create', compact('breadcrumbs'));
    }

    public function store(Request $request)
{
    $data = $request->validate([
        'rut' => ['required', 'string', 'max:20', 'unique:instructors,rut'],
        'name' => ['required', 'string', 'max:255'],
        'email' => ['nullable', 'email', 'max:255'],
        'phone' => ['nullable', 'string', 'max:50'],
        'profession' => ['nullable', 'string', 'max:255'],
        'bio' => ['nullable', 'string'],

        // ✅ archivos
        'documents' => ['nullable', 'array'],
        'documents.*' => ['file', 'mimes:pdf', 'max:5120'], // 5MB
    ]);

    $instructor = Instructor::create($data);

    // ✅ subir docs si venían
    $this->appendUploadedDocuments($request, $instructor);

    return redirect()
        ->route('instructors.index')
        ->with('status', 'Relator creado correctamente.');
}


    public function edit(Instructor $instructor)
    {
        $breadcrumbs = [
            ['label' => 'Relatores', 'url' => route('instructors.index')],
            ['label' => 'Editar relator'],
        ];

        return view('instructors.edit', compact('instructor', 'breadcrumbs'));
    }

public function update(Request $request, Instructor $instructor)
{
    $data = $request->validate([
        'rut' => ['required', 'string', 'max:20', 'unique:instructors,rut,' . $instructor->id],
        'name' => ['required', 'string', 'max:255'],
        'email' => ['nullable', 'email', 'max:255'],
        'phone' => ['nullable', 'string', 'max:50'],
        'profession' => ['nullable', 'string', 'max:255'],
        'bio' => ['nullable', 'string'],
        'document_label' => ['nullable', 'string', 'max:50'],
        'documents' => ['nullable', 'array'],
        'documents.*' => ['file', 'mimes:pdf', 'max:5120'],
    ]);

    $instructor->update($data);

    // ✅ subir docs (solo UNA vez)
    $this->appendUploadedDocuments($request, $instructor);

    return redirect()
        ->route('instructors.index')
        ->with('status', 'Relator actualizado correctamente.');
}

    public function destroy(Instructor $instructor)
    {
        $instructor->delete();

        return redirect()
            ->route('instructors.index')
            ->with('status', 'Relator eliminado correctamente.');
    }

    // JSON para modal detalle (como Empresas)
    public function show(Instructor $instructor)
    {
        return response()->json($instructor);
    }
public function documentShow(Instructor $instructor, int $index)
{
    $docs = $instructor->documents ?? [];
    abort_unless(isset($docs[$index]), 404);

    $doc = $docs[$index];
    $path = $doc['path'] ?? null;

    abort_unless(is_string($path) && $path !== '', 404);
    abort_unless(Storage::disk('public')->exists($path), 404);

    return Storage::disk('public')->response($path);
}


public function documentDownload(Instructor $instructor, int $index)
{
    $path = $doc['path'] ?? null;
    abort_unless(is_string($path) && $path !== '', 404);

    $doc = $docs[$index];
    abort_unless(Storage::disk('public')->exists($doc['path']), 404);

    return Storage::disk('public')->download($doc['path'], $doc['name'] ?? 'documento.pdf');
}

public function documentDelete(Instructor $instructor, int $index)
{
    $docs = $instructor->documents ?? [];
    abort_unless(isset($docs[$index]), 404);

    $doc = $docs[$index];

    if (!empty($doc['path']) && Storage::disk('public')->exists($doc['path'])) {
        Storage::disk('public')->delete($doc['path']);
    }

    array_splice($docs, $index, 1);
    $instructor->documents = $docs;
    $instructor->save();

    return back()->with('status', 'Documento eliminado.');
}
private function appendUploadedDocuments(Request $request, Instructor $instructor): void
{
    // 1) limpiar existing: solo dejamos arrays con 'path'
    $existing = $instructor->documents ?? [];
    $existing = array_values(array_filter($existing, function ($d) {
        return is_array($d) && !empty($d['path'] ?? null);
    }));

    // 2) si no vienen archivos, igual guardamos la limpieza
    if (!$request->hasFile('documents')) {
        $instructor->documents = $existing;
        $instructor->save();
        return;
    }
    $label = trim((string) $request->input('document_label', 'Documento'));
    if ($label === '') $label = 'Documento';
    foreach ($request->file('documents') as $file) {
        if (!$file) continue;

        $safeBase = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $ext = $file->getClientOriginalExtension();
        $filename = $safeBase . '_' . Str::random(8) . '.' . $ext;

        $path = $file->storeAs(
            'instructors/' . $instructor->id,
            $filename,
            'public'
        );

        $existing[] = [
            'label' => $label,
            'path' => $path,
            'name' => $file->getClientOriginalName(),
            'mime' => $file->getClientMimeType(),
            'size' => $file->getSize(),
            'uploaded_at' => now()->toDateTimeString(),
        ];

    }

    $instructor->documents = $existing;
    $instructor->save();
}
public function showPage(Instructor $instructor)
{
    $breadcrumbs = [
        ['label' => 'Relatores', 'url' => route('instructors.index')],
        ['label' => $instructor->name],
    ];

    return view('instructors.show', compact('instructor', 'breadcrumbs'));
}

}
