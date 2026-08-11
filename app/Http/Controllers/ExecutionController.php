<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Execution;

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
        $participants = \App\Models\Participant::orderBy('first_name')->get();

        $breadcrumbs = [
            ['label' => 'Ejecuciones', 'url' => route('executions.index')],
            ['label' => 'Nueva ejecución']
        ];

        return view('executions.create', compact(
            'courses',
            'companies',
            'participants',
            'breadcrumbs'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => ['required', 'in:abierto,cerrado'],
            'course_id' => ['required', 'exists:courses,id'],
            'company_id' => ['required', 'exists:companies,id'],
            'evaluation_type' => ['required', 'in:percentage,grade'],
            'start_date' => ['required', 'date'],

            'hours_per_day' => ['required', 'numeric', 'min:1'],


            'observations' => ['nullable', 'string'],

            'participants' => ['nullable', 'array'],
            'participants.*' => ['exists:participants,id'],
        ]);

        $course = \App\Models\Course::findOrFail($validated['course_id']);

        $execution = \App\Models\Execution::create([
            'type' => $validated['type'],

            'course_id' => $course->id,
            'company_id' => $validated['company_id'],

            // Snapshots
            'course_name' => $course->name,
            'course_hours' => $course->hours,

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

            // Planificación
            'hours_per_day' => $validated['hours_per_day'],


            'evaluation_type' => $validated['evaluation_type'],
            'start_date' => $validated['start_date'],

            'status' => 'planificada',

            'observations' => $validated['observations'] ?? null,
        ]);

        if (!empty($validated['participants'])) {
            $execution->participants()->syncWithoutDetaching(
                $validated['participants']
            );
        }

        return redirect()
            ->route('executions.index')
            ->with('status', 'Ejecución creada correctamente.');
    }
    public function show(
        \App\Models\Execution $execution,
        \App\Services\PlanningValidationService $planningValidationService,
        \App\Services\SurveyService $surveyService
    )
    {
        $execution->load([
            'course',
            'company',
            'planning',
            'sessions.attendances',
            'participants',
            'instructors',
            'evaluations',
            'surveyResponses',
        ]);

        $participants = \App\Models\Participant::orderBy('first_name')->get();
        $instructors = \App\Models\Instructor::orderBy('name')->get();

        $planningValidation = $planningValidationService->validate($execution);
        $surveySummary = $surveyService->summary($execution);

        $breadcrumbs = [
            ['label' => 'Ejecuciones', 'url' => route('executions.index')],
            ['label' => $execution->internal_code]
        ];
        $tab = request('tab', 'general');
        return view('executions.show', compact(
            'execution',
            'participants',
            'instructors',
            'breadcrumbs',
            'tab',
            'planningValidation',
            'surveySummary'
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
    public function update(Request $request, Execution $execution)
    {
        $validated = $request->validate([
            'type' => ['required', 'in:abierto,cerrado'],
            'course_id' => ['required', 'exists:courses,id'],
            'company_id' => ['required', 'exists:companies,id'],
            'evaluation_type' => ['required', 'in:percentage,grade'],
            'start_date' => ['required', 'date'],

            'hours_per_day' => ['required', 'numeric', 'min:1'],


            'status' => ['required', 'in:planificada,en_ejecucion,finalizada,cancelada'],
            'observations' => ['nullable', 'string'],
        ]);

        $course = \App\Models\Course::findOrFail($validated['course_id']);

        $execution->update([
            'type' => $validated['type'],

            'course_id' => $course->id,
            'company_id' => $validated['company_id'],

            // Snapshots
            'course_name' => $course->name,
            'course_hours' => $course->hours,

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

            // Planificación
            'hours_per_day' => $validated['hours_per_day'],


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

    /**
     * Cerrar la ejecución (marcar como finalizada).
     *
     * Bloquea la edición de Planificación, Agenda,
     * Asistencia y Notas hasta que se reabra.
     */
    public function close(\App\Models\Execution $execution)
    {
        $execution->update([
            'status' => 'finalizada',
        ]);

        return redirect()
            ->route('executions.show', [
                $execution,
                'tab' => 'general',
            ])
            ->with('status', 'Ejecución cerrada correctamente.');
    }

    /**
     * Reabrir la ejecución para volver a editarla.
     */
    public function reopen(\App\Models\Execution $execution)
    {
        $execution->update([
            'status' => 'en_ejecucion',
        ]);

        return redirect()
            ->route('executions.show', [
                $execution,
                'tab' => 'general',
            ])
            ->with('status', 'Ejecución reabierta correctamente.');
    }
    public function addParticipant(Request $request, Execution $execution)
    {
        $validated = $request->validate([
            'participant_id' => ['required', 'exists:participants,id'],
        ]);

        $execution->participants()->syncWithoutDetaching([
            $validated['participant_id']
        ]);

        return redirect()
            ->route('executions.show', [
                $execution,
                'tab' => 'participants'
            ])
            ->with('status', 'Participante agregado correctamente.');
    }
    public function addInstructor(
        Request $request,
        Execution $execution
    ) {
        $validated = $request->validate([
            'instructor_id' => ['required', 'exists:instructors,id'],
        ]);

        $execution->instructors()->syncWithoutDetaching([
            $validated['instructor_id']
        ]);

        return redirect()
            ->route('executions.show', [
                $execution,
                'tab' => 'instructors'
            ])
            ->with('status', 'Relator agregado correctamente.');
    }
    public function removeInstructor(
        \App\Models\Execution $execution,
        \App\Models\Instructor $instructor
    ) {
        $execution->instructors()->detach($instructor->id);

        return redirect()
            ->route('executions.show', [
                $execution,
                'tab' => 'instructors'
            ])
            ->with('status', 'Relator eliminado correctamente.');
    }
    public function removeParticipant(
        \App\Models\Execution $execution,
        \App\Models\Participant $participant
    ) {
        $execution->participants()->detach($participant->id);

        return redirect()
            ->route('executions.show', [
                $execution,
                'tab' => 'participants'
            ])
            ->with('status', 'Participante eliminado correctamente.');
    }
    public function addCompanyParticipants(\App\Models\Execution $execution)
    {
        $participantIds = \App\Models\Participant::query()
            ->where('company_id', $execution->company_id)
            ->pluck('id')
            ->toArray();

        $execution->participants()->syncWithoutDetaching($participantIds);

        return redirect()
            ->route('executions.show', [
                $execution,
                'tab' => 'participants'
            ])
            ->with('status', 'Participantes agregados correctamente.');
    }
    public function removeAllParticipants(
        \App\Models\Execution $execution
    ) {
        $execution->participants()->detach();

        return redirect()
            ->route('executions.show', [
                $execution,
                'tab' => 'participants'
            ])
            ->with('status', 'Todos los participantes fueron eliminados.');
    }
}
