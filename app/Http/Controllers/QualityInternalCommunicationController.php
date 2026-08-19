<?php

namespace App\Http\Controllers;

use App\Models\QualityInternalCommunication;
use Illuminate\Http\Request;

class QualityInternalCommunicationController extends Controller
{
    public function index()
    {
        $communications = QualityInternalCommunication::orderByDesc('communicated_at')->paginate(20);

        return view('quality.internal-communications.index', compact('communications'));
    }

    public function create()
    {
        return view('quality.internal-communications.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateData($request);

        QualityInternalCommunication::create($validated);

        return redirect()
            ->route('quality.internal-communications.index')
            ->with('status', 'Comunicación registrada correctamente.');
    }

    public function edit(QualityInternalCommunication $communication)
    {
        return view('quality.internal-communications.edit', compact('communication'));
    }

    public function update(Request $request, QualityInternalCommunication $communication)
    {
        $validated = $this->validateData($request);

        $communication->update($validated);

        return redirect()
            ->route('quality.internal-communications.index')
            ->with('status', 'Comunicación actualizada correctamente.');
    }

    public function destroy(QualityInternalCommunication $communication)
    {
        $communication->delete();

        return redirect()
            ->route('quality.internal-communications.index')
            ->with('status', 'Registro eliminado.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'topics' => ['required', 'array', 'min:1'],
            'topics.*' => ['in:politica,requisitos_norma,objetivos,desempeno'],
            'channel' => ['required', 'string', 'max:255'],
            'audience' => ['required', 'string', 'max:255'],
            'communicated_at' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ]);
    }
}
