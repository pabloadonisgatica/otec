<?php

namespace App\Http\Controllers;

use App\Models\QualityPolicy;
use App\Models\QualityPolicyCommitment;
use App\Models\QualityPolicyObjective;
use Illuminate\Http\Request;

class QualityPolicyController extends Controller
{
    public function edit()
    {
        $policy = QualityPolicy::current();
        $policy->load('commitments.objectives');

        return view('quality.policy', compact('policy'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'title_1' => ['nullable', 'string', 'max:255'],
            'title_2' => ['nullable', 'string', 'max:255'],
            'detail' => ['nullable', 'string'],
            'signature_name' => ['nullable', 'string', 'max:255'],
            'signature_position' => ['nullable', 'string', 'max:255'],
            'approval_date' => ['nullable', 'date'],
        ]);

        QualityPolicy::current()->update($validated);

        return redirect()
            ->route('quality.policy.edit')
            ->with('status', 'Política de Calidad actualizada correctamente.');
    }

    public function storeCommitment(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string'],
        ]);

        QualityPolicyCommitment::create([
            'quality_policy_id' => QualityPolicy::current()->id,
            'title' => $validated['title'],
        ]);

        return redirect()
            ->route('quality.policy.edit')
            ->with('status', 'Compromiso agregado correctamente.');
    }

    public function updateCommitment(Request $request, QualityPolicyCommitment $commitment)
    {
        $validated = $request->validate([
            'title' => ['required', 'string'],
        ]);

        $commitment->update($validated);

        return redirect()
            ->route('quality.policy.edit')
            ->with('status', 'Compromiso actualizado correctamente.');
    }

    public function destroyCommitment(QualityPolicyCommitment $commitment)
    {
        $commitment->delete();

        return redirect()
            ->route('quality.policy.edit')
            ->with('status', 'Compromiso eliminado (junto con sus objetivos).');
    }

    public function storeObjective(Request $request, QualityPolicyCommitment $commitment)
    {
        $validated = $request->validate([
            'title' => ['required', 'string'],
        ]);

        $commitment->objectives()->create($validated);

        return redirect()
            ->route('quality.policy.edit')
            ->with('status', 'Objetivo agregado correctamente.');
    }

    public function updateObjective(Request $request, QualityPolicyObjective $objective)
    {
        $validated = $request->validate([
            'title' => ['required', 'string'],
        ]);

        $objective->update($validated);

        return redirect()
            ->route('quality.policy.edit')
            ->with('status', 'Objetivo actualizado correctamente.');
    }

    public function destroyObjective(QualityPolicyObjective $objective)
    {
        $objective->delete();

        return redirect()
            ->route('quality.policy.edit')
            ->with('status', 'Objetivo eliminado.');
    }
}
