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

        // Documentos: cada uno con su archivo y su tipo (CV, Título…)
        'documents' => ['nullable', 'array', 'max:20'],
        'documents.*.file' => ['required', 'file', 'mimes:pdf', 'max:5120'], // 5MB
        'documents.*.label' => ['nullable', 'string', 'max:80'],
    ], [
        'documents.max' => 'Puedes subir hasta 20 documentos por vez.',
        'documents.*.file.required' => 'Falta el archivo de uno de los documentos.',
        'documents.*.file.mimes' => 'Los documentos deben ser PDF.',
        'documents.*.file.max' => 'Cada documento puede pesar hasta 5 MB.',
    ]);

    $instructor = Instructor::create(\Illuminate\Support\Arr::except($data, ['documents']));

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
        // Documentos: cada uno con su archivo y su tipo (CV, Título…)
        'documents' => ['nullable', 'array', 'max:20'],
        'documents.*.file' => ['required', 'file', 'mimes:pdf', 'max:5120'], // 5MB
        'documents.*.label' => ['nullable', 'string', 'max:80'],
    ], [
        'documents.max' => 'Puedes subir hasta 20 documentos por vez.',
        'documents.*.file.required' => 'Falta el archivo de uno de los documentos.',
        'documents.*.file.mimes' => 'Los documentos deben ser PDF.',
        'documents.*.file.max' => 'Cada documento puede pesar hasta 5 MB.',
    ]);

    $instructor->update(\Illuminate\Support\Arr::except($data, ['documents']));

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
    // Los documentos llegan anidados (documents[i][file]): hasFile('documents')
    // solo revisa el primer nivel y no los ve, por eso se revisa la lista directo.
    $rows = $request->file('documents');

    if (!is_array($rows) || $rows === []) {
        $instructor->documents = $existing;
        $instructor->save();
        return;
    }
    // Cada fila: documents[i][file] + documents[i][label] (su propio tipo de documento)
    foreach ($rows as $index => $row) {
        $file = $row['file'] ?? null;
        if (!$file) continue;

        $label = trim((string) $request->input("documents.{$index}.label", ''));
        if ($label === '') $label = 'Documento';

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
