<?php

namespace App\Http\Controllers;

use App\Models\QualityRecord;
use Illuminate\Http\Request;

class QualityRecordController extends Controller
{
    public function index()
    {
        $records = QualityRecord::orderBy('name')->paginate(20);

        return view('quality.records.index', compact('records'));
    }

    public function create()
    {
        return view('quality.records.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateData($request);

        $record = QualityRecord::create($validated);

        return redirect()
            ->route('quality.records.show', $record)
            ->with('status', 'Registro creado correctamente.');
    }

    public function show(QualityRecord $record)
    {
        return view('quality.records.show', compact('record'));
    }

    public function edit(QualityRecord $record)
    {
        return view('quality.records.edit', compact('record'));
    }

    public function update(Request $request, QualityRecord $record)
    {
        $validated = $this->validateData($request);

        $record->update($validated);

        return redirect()
            ->route('quality.records.show', $record)
            ->with('status', 'Registro actualizado correctamente.');
    }

    public function destroy(QualityRecord $record)
    {
        $record->delete();

        return redirect()
            ->route('quality.records.index')
            ->with('status', 'Registro eliminado.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'approval_user' => ['nullable', 'string', 'max:255'],
            'approval_date' => ['nullable', 'date'],
            'protection' => ['nullable', 'string', 'max:255'],
            'storage_location' => ['nullable', 'string', 'max:255'],
            'retention_time' => ['nullable', 'string', 'max:255'],
            'recovery' => ['nullable', 'string', 'max:255'],
            'final_disposition' => ['nullable', 'string', 'max:255'],
        ]);
    }
}
