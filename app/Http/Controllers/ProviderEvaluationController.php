<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProviderEvaluationRequest;
use App\Models\Provider;
use App\Models\ProviderEvaluation;
use App\Models\User;
use App\Services\ProviderEvaluationService;

/**
 * NCH 2728 — 7.4 Evaluación de Proveedores (dentro de cada proveedor).
 */
class ProviderEvaluationController extends Controller
{
    public function __construct(private ProviderEvaluationService $service)
    {
    }

    public function create(Provider $provider)
    {
        $evaluation = new ProviderEvaluation([
            'type' => $provider->evaluations()->exists() ? 'reevaluation' : 'selection',
            'evaluated_at' => now(),
            'evaluator_id' => auth()->id(),
            'next_evaluation_at' => now()->addMonths(ProviderEvaluation::DEFAULT_REEVALUATION_MONTHS),
            'scores' => [],
        ]);

        return view('quality.providers.evaluations.create', [
            'provider' => $provider,
            'evaluation' => $evaluation,
            'users' => User::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(ProviderEvaluationRequest $request, Provider $provider)
    {
        $evaluation = $this->service->create($provider, $request->validated());

        return redirect()
            ->route('quality.providers.show', $provider)
            ->with('status', "Evaluación registrada: {$evaluation->resultLabel()} ({$evaluation->scoreLabel()}).");
    }

    public function edit(Provider $provider, ProviderEvaluation $evaluation)
    {
        return view('quality.providers.evaluations.edit', [
            'provider' => $provider,
            'evaluation' => $evaluation,
            'users' => User::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(ProviderEvaluationRequest $request, Provider $provider, ProviderEvaluation $evaluation)
    {
        $this->service->update($evaluation, $request->validated());

        return redirect()
            ->route('quality.providers.show', $provider)
            ->with('status', "Evaluación actualizada: {$evaluation->resultLabel()} ({$evaluation->scoreLabel()}).");
    }

    public function destroy(Provider $provider, ProviderEvaluation $evaluation)
    {
        $evaluation->delete();

        return redirect()
            ->route('quality.providers.show', $provider)
            ->with('status', 'Evaluación eliminada.');
    }
}
