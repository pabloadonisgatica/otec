<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    private array $map = [
        'instructors' => \App\Models\Instructor::class,
        'users' => \App\Models\User::class,
        // 'companies' => \App\Models\Company::class,
        // 'courses' => \App\Models\Course::class,
    ];

    private function resolveModel(string $type)
    {
        abort_unless(isset($this->map[$type]), 404);
        return $this->map[$type];
    }

    private function getDoc($model, int $index): array
    {
        $docs = $model->documents ?? [];
        abort_unless(is_array($docs) && isset($docs[$index]), 404);

        $doc = $docs[$index];
        abort_unless(is_array($doc), 404);

        $path = $doc['path'] ?? null;
        abort_unless(is_string($path) && $path !== '', 404);

        return $doc;
    }

    public function show(string $type, int $id, int $index)
    {
        $modelClass = $this->resolveModel($type);
        $model = $modelClass::findOrFail($id);

        $doc = $this->getDoc($model, $index);

        abort_unless(Storage::disk('public')->exists($doc['path']), 404);

        return Storage::disk('public')->response($doc['path']);
    }

    public function download(string $type, int $id, int $index)
    {
        $modelClass = $this->resolveModel($type);
        $model = $modelClass::findOrFail($id);

        $doc = $this->getDoc($model, $index);

        abort_unless(Storage::disk('public')->exists($doc['path']), 404);

        return Storage::disk('public')->download(
            $doc['path'],
            $doc['name'] ?? 'documento.pdf'
        );
    }

    public function delete(Request $request, string $type, int $id, int $index)
    {
        $modelClass = $this->resolveModel($type);
        $model = $modelClass::findOrFail($id);

        $docs = $model->documents ?? [];
        abort_unless(is_array($docs) && isset($docs[$index]), 404);

        $doc = $docs[$index];
        $path = is_array($doc) ? ($doc['path'] ?? null) : null;

        if (is_string($path) && $path !== '' && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }

        array_splice($docs, $index, 1);
        $model->documents = array_values($docs);
        $model->save();

        return back()->with('status', 'Documento eliminado.');
    }
}
