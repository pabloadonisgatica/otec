<?php

namespace App\Http\Controllers;

use App\Models\QualityDocument;
use App\Models\QualityJobProfile;
use Illuminate\Http\Request;

class QualityJobProfileController extends Controller
{
    public function index()
    {
        $profiles = QualityJobProfile::orderByDesc('profile_date')->paginate(20);

        return view('quality.job-profiles.index', compact('profiles'));
    }

    public function create()
    {
        $procedures = QualityDocument::where('type', 'procedimiento')->orderBy('name')->get();

        return view('quality.job-profiles.create', compact('procedures'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateData($request);

        $profile = QualityJobProfile::create($validated);

        $profile->procedures()->sync($request->input('procedure_ids', []));

        return redirect()
            ->route('quality.job-profiles.show', $profile)
            ->with('status', 'Perfil de Cargo creado correctamente.');
    }

    public function show(QualityJobProfile $jobProfile)
    {
        $jobProfile->load('procedures');

        return view('quality.job-profiles.show', ['profile' => $jobProfile]);
    }

    public function edit(QualityJobProfile $jobProfile)
    {
        $procedures = QualityDocument::where('type', 'procedimiento')->orderBy('name')->get();
        $assignedIds = $jobProfile->procedures()->pluck('quality_documents.id')->all();

        return view('quality.job-profiles.edit', [
            'profile' => $jobProfile,
            'procedures' => $procedures,
            'assignedIds' => $assignedIds,
        ]);
    }

    public function update(Request $request, QualityJobProfile $jobProfile)
    {
        $validated = $this->validateData($request);

        $jobProfile->update($validated);

        $jobProfile->procedures()->sync($request->input('procedure_ids', []));

        return redirect()
            ->route('quality.job-profiles.show', $jobProfile)
            ->with('status', 'Perfil de Cargo actualizado correctamente.');
    }

    public function destroy(QualityJobProfile $jobProfile)
    {
        $jobProfile->delete();

        return redirect()
            ->route('quality.job-profiles.index')
            ->with('status', 'Perfil de Cargo eliminado.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'profile_date' => ['nullable', 'date'],
            'position_name' => ['required', 'string', 'max:255'],
            'functions' => ['nullable', 'string'],
            'responsibilities' => ['nullable', 'string'],
            'area' => ['nullable', 'string', 'max:255'],
            'reports_to' => ['nullable', 'string', 'max:255'],
            'direct_reports' => ['nullable', 'string', 'max:255'],
            'education_requirements' => ['nullable', 'string'],
            'training_requirements' => ['nullable', 'string'],
            'skills_requirements' => ['nullable', 'string'],
            'experience_requirements' => ['nullable', 'string'],
        ]);
    }
}
