<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\QualityInternalTraining;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class QualityInternalTrainingController extends Controller
{
    public function index()
    {
        $trainings = QualityInternalTraining::orderByDesc('activity_date')->paginate(20);

        return view('quality.internal-trainings.index', compact('trainings'));
    }

    public function create()
    {
        return view('quality.internal-trainings.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateData($request);

        $training = QualityInternalTraining::create($validated);

        $this->syncParticipants($request, $training);

        return redirect()
            ->route('quality.internal-trainings.index')
            ->with('status', 'Registro de capacitación interna creado correctamente.');
    }

    public function edit(QualityInternalTraining $internalTraining)
    {
        $internalTraining->load('participants');

        return view('quality.internal-trainings.edit', ['training' => $internalTraining]);
    }

    public function update(Request $request, QualityInternalTraining $internalTraining)
    {
        $validated = $this->validateData($request);

        $internalTraining->update($validated);

        $this->syncParticipants($request, $internalTraining);

        return redirect()
            ->route('quality.internal-trainings.index')
            ->with('status', 'Registro actualizado correctamente.');
    }

    public function destroy(QualityInternalTraining $internalTraining)
    {
        $internalTraining->delete();

        return redirect()
            ->route('quality.internal-trainings.index')
            ->with('status', 'Registro eliminado.');
    }

    public function pdf(QualityInternalTraining $internalTraining)
    {
        $internalTraining->load('participants');

        $otecName = AppSetting::get('otec_name', 'Nombre de la OTEC no configurado');

        $html = view('quality.internal-trainings.pdf', [
            'training' => $internalTraining,
            'otecName' => $otecName,
        ])->render();

        $pdf = Pdf::loadHTML($html)->setPaper('letter', 'portrait');

        return $pdf->stream('capacitacion-interna-' . $internalTraining->id . '.pdf');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'activity_date' => ['required', 'date'],
            'activity_name' => ['required', 'string', 'max:255'],
            'objective' => ['nullable', 'string'],
            'instructor_name' => ['nullable', 'string', 'max:255'],
            'hours' => ['nullable', 'integer', 'min:0'],
            'objective_met' => ['nullable', 'boolean'],
            'compliance_description' => ['nullable', 'string'],
            'additional_actions' => ['nullable', 'string'],
        ]);
    }

    private function syncParticipants(Request $request, QualityInternalTraining $training): void
    {
        $training->participants()->delete();

        $names = $request->input('participant_name', []);
        $positions = $request->input('participant_position', []);

        foreach ($names as $index => $name) {
            $name = trim((string) $name);
            if ($name === '') continue;

            $training->participants()->create([
                'name' => $name,
                'position' => $positions[$index] ?? null,
            ]);
        }
    }
}
