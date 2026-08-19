<?php

namespace App\Http\Controllers;

use App\Models\QualityRepresentative;
use Illuminate\Http\Request;

class QualityRepresentativeController extends Controller
{
    public function index()
    {
        $current = QualityRepresentative::current();

        $history = QualityRepresentative::query()
            ->when($current, fn ($query) => $query->where('id', '!=', $current->id))
            ->orderByDesc('start_date')
            ->get();

        return view('quality.representatives', compact('current', 'history'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        // Cierra al representante vigente (si hay uno) en la fecha
        // de inicio del nuevo, para no dejar huecos ni superposición.
        $current = QualityRepresentative::current();

        if ($current) {
            $current->update(['end_date' => $validated['start_date']]);
        }

        QualityRepresentative::create($validated);

        return redirect()
            ->route('quality.representatives.index')
            ->with('status', 'Representante de la Dirección asignado correctamente.');
    }

    public function destroy(QualityRepresentative $representative)
    {
        $representative->delete();

        return redirect()
            ->route('quality.representatives.index')
            ->with('status', 'Registro eliminado del historial.');
    }
}
