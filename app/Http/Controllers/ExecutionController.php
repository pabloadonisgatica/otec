<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExecutionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
   public function index()
    {
        $executions = \App\Models\Execution::with([
            'course',
            'company',
            'sessions',
            'participants',
            'instructors'
        ])
        ->latest()->paginate(10);

        return view('executions.index', compact('executions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $courses = \App\Models\Course::orderBy('name')->get();
        $companies = \App\Models\Company::orderBy('name')->get();

        $breadcrumbs = [
            ['label' => 'Inicio', 'url' => route('dashboard')],
            ['label' => 'Ejecuciones', 'url' => route('executions.index')],
            ['label' => 'Nueva ejecución']
        ];

        return view('executions.create', compact(
            'courses',
            'companies',
            'breadcrumbs'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(\Illuminate\Http\Request $request)
    {
        $validated = $request->validate([
            'type' => ['required', 'in:abierto,cerrado'],
            'course_id' => ['required', 'exists:courses,id'],
            'company_id' => ['required', 'exists:companies,id'],
            'evaluation_type' => ['required', 'in:percentage,grade'],
            'start_date' => ['required', 'date'],
            'observations' => ['nullable', 'string'],
        ]);

        $course = \App\Models\Course::findOrFail($validated['course_id']);

        $execution = \App\Models\Execution::create([
            'course_id' => $course->id,
            'company_id' => $validated['company_id'],
            'course_name' => $course->name,      // snapshot
            'modality' => collect($course->instruction_modalities ?? [])
                ->map(function ($m) {
                    return [
                        'presencial' => 'Presencial',
                        'elearning_sync' => 'E-learning Sync',
                        'elearning_async' => 'E-learning Async',
                        'distance_self' => 'Autoaprendizaje',
                    ][$m] ?? $m;
                })
                ->join(' · '),
            'evaluation_type' => $validated['evaluation_type'],
            'start_date' => $validated['start_date'],
            'status' => 'planificada',
            'observations' => $validated['observations'] ?? null,
        ]);

        return redirect()
            ->route('executions.index')
            ->with('status', 'Ejecución creada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(\App\Models\Execution $execution)
    {
        $execution->load([
            'course',
            'company',
            'sessions',
            'participants',
            'instructors'
        ]);

        $breadcrumbs = [
            ['label' => 'Inicio', 'url' => route('dashboard')],
            ['label' => 'Ejecuciones', 'url' => route('executions.index')],
            ['label' => $execution->internal_code]
        ];

        return view('executions.show', compact(
            'execution',
            'breadcrumbs'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(\App\Models\Execution $execution)
    {
        $courses = \App\Models\Course::orderBy('name')->get();
        $companies = \App\Models\Company::orderBy('name')->get();

        $breadcrumbs = [
            ['label' => 'Inicio', 'url' => route('dashboard')],
            ['label' => 'Ejecuciones', 'url' => route('executions.index')],
            ['label' => $execution->internal_code, 'url' => route('executions.show', $execution)],
            ['label' => 'Editar']
        ];

        return view('executions.edit', compact(
            'execution',
            'courses',
            'companies',
            'breadcrumbs'
        ));
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(\Illuminate\Http\Request $request, \App\Models\Execution $execution)
{
    $validated = $request->validate([
        'type' => ['required', 'in:abierto,cerrado'],
        'course_id' => ['required', 'exists:courses,id'],
        'company_id' => ['required', 'exists:companies,id'],
        'evaluation_type' => ['required', 'in:percentage,grade'],
        'start_date' => ['required', 'date'],
        'status' => ['required', 'in:planificada,en_ejecucion,finalizada,cancelada'],
        'observations' => ['nullable', 'string'],
    ]);

    $course = \App\Models\Course::findOrFail($validated['course_id']);

    $execution->update([
        'type' => $validated['type'],
        'course_id' => $course->id,
        'company_id' => $validated['company_id'],
        'course_name' => $course->name,
        'modality' => collect($course->instruction_modalities ?? [])
    ->map(function ($m) {
        return [
            'presencial' => 'Presencial',
            'elearning_sync' => 'E-learning Sync',
            'elearning_async' => 'E-learning Async',
            'distance_self' => 'Autoaprendizaje',
        ][$m] ?? $m;
    })
    ->join(' · '),
        'evaluation_type' => $validated['evaluation_type'],
        'start_date' => $validated['start_date'],
        'status' => $validated['status'],
        'observations' => $validated['observations'] ?? null,
    ]);

    return redirect()
        ->route('executions.show', $execution)
        ->with('status', 'Ejecución actualizada correctamente.');
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(\App\Models\Execution $execution)
    {
        $execution->delete();

        return redirect()
            ->route('executions.index')
            ->with('status', 'Ejecución eliminada correctamente.');
    }
}
