<?php

namespace App\Http\Controllers;

use App\Models\CorrectiveAction;
use App\Models\NonConformity;
use Illuminate\Http\Request;

class CorrectiveActionController extends Controller
{
    public function store(Request $request, NonConformity $non_conformity)
    {
        $validated = $request->validate([
            'type' => ['required', 'in:corrective,preventive'],
            'description' => ['required', 'string'],
            'responsible_id' => ['nullable', 'exists:users,id'],
            'due_date' => ['nullable', 'date'],
        ]);

        $validated['non_conformity_id'] = $non_conformity->id;

        CorrectiveAction::create($validated);

        return redirect()
            ->route('quality.non-conformities.show', $non_conformity)
            ->with('status', 'Acción agregada correctamente.');
    }

    public function update(Request $request, NonConformity $non_conformity, CorrectiveAction $action)
    {
        abort_unless($action->non_conformity_id === $non_conformity->id, 404);

        $validated = $request->validate([
            'type' => ['required', 'in:corrective,preventive'],
            'description' => ['required', 'string'],
            'responsible_id' => ['nullable', 'exists:users,id'],
            'due_date' => ['nullable', 'date'],
            'status' => ['required', 'in:pending,in_progress,completed'],
            'evidence' => ['nullable', 'string'],
            'implemented_at' => ['nullable', 'date'],
            'implementation_verified_at' => ['nullable', 'date'],
            'verifier_name' => ['nullable', 'string', 'max:255'],
            'effectiveness_verified_at' => ['nullable', 'date'],
            'effectiveness_satisfactory' => ['nullable', 'in:1,0'],
            'effectiveness_notes' => ['nullable', 'string'],
        ]);

        $wasCompleted = $action->isCompleted();
        $willBeCompleted = $validated['status'] === 'completed';

        if (! $wasCompleted && $willBeCompleted) {
            $validated['completed_at'] = now();
        } elseif (! $willBeCompleted) {
            $validated['completed_at'] = null;
        }

        $action->update($validated);

        return redirect()
            ->route('quality.non-conformities.show', $non_conformity)
            ->with('status', 'Acción actualizada correctamente.');
    }

    public function destroy(NonConformity $non_conformity, CorrectiveAction $action)
    {
        abort_unless($action->non_conformity_id === $non_conformity->id, 404);

        $action->delete();

        return redirect()
            ->route('quality.non-conformities.show', $non_conformity)
            ->with('status', 'Acción eliminada.');
    }
}
