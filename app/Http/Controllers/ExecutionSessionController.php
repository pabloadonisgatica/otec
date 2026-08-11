<?php

namespace App\Http\Controllers;

use App\Models\Execution;
use App\Models\ExecutionSession;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ExecutionSessionController extends Controller
{
    /**
     * Mostrar formulario de edición.
     */
    public function edit(
        Execution $execution,
        ExecutionSession $session
    ) {
        abort_unless(
            $session->execution_id === $execution->id,
            404
        );

        if ($execution->isFinalized()) {
            return redirect()
                ->route('executions.show', [$execution, 'tab' => 'sessions'])
                ->with('status', 'La ejecución está finalizada. Reábrela para poder editar la agenda.');
        }

        return view(
            'executions.sessions.edit',
            compact('execution', 'session')
        );
    }

    /**
     * Mostrar formulario de creación.
     */
    public function create(Execution $execution)
    {
        if ($execution->isFinalized()) {
            return redirect()
                ->route('executions.show', [$execution, 'tab' => 'sessions'])
                ->with('status', 'La ejecución está finalizada. Reábrela para poder editar la agenda.');
        }

        return view(
            'executions.sessions.create',
            compact('execution')
        );
    }

    /**
     * Crear sesión manual.
     */
    public function store(
        Request $request,
        Execution $execution
    ) {
        abort_if(
            $execution->isFinalized(),
            403,
            'La ejecución está finalizada. Reábrela para poder editar la agenda.'
        );

        $validated = $request->validate([
            'session_date' => ['required', 'date'],
            'start_time'   => ['required'],
            'hours'        => ['required', 'numeric', 'min:0.5'],
        ]);

        $validated['end_time'] = Carbon::parse($validated['start_time'])
            ->addMinutes($validated['hours'] * 60)
            ->format('H:i:s');

        ExecutionSession::create([
            'execution_id' => $execution->id,
            ...$validated,
        ]);

        return redirect()
            ->route('executions.show', [
                $execution,
                'tab' => 'sessions',
            ])
            ->with(
                'status',
                'Sesión agregada correctamente.'
            );
    }

    /**
     * Actualizar sesión.
     */
    public function update(
        Request $request,
        Execution $execution,
        ExecutionSession $session
    ) {
        abort_unless(
            $session->execution_id === $execution->id,
            404
        );

        abort_if(
            $execution->isFinalized(),
            403,
            'La ejecución está finalizada. Reábrela para poder editar la agenda.'
        );

        $validated = $request->validate([
            'session_date' => ['required', 'date'],
            'start_time'   => ['required'],
            'hours'        => ['required', 'numeric', 'min:0.5'],
        ]);

        $validated['end_time'] = Carbon::parse($validated['start_time'])
            ->addMinutes($validated['hours'] * 60)
            ->format('H:i:s');

        $session->update($validated);

        return redirect()
            ->route('executions.show', [
                $execution,
                'tab' => 'sessions',
            ])
            ->with(
                'status',
                'Sesión actualizada correctamente.'
            );
    }

    /**
     * Eliminar sesión.
     */
    public function destroy(
        Execution $execution,
        ExecutionSession $session
    ) {
        abort_unless(
            $session->execution_id === $execution->id,
            404
        );

        abort_if(
            $execution->isFinalized(),
            403,
            'La ejecución está finalizada. Reábrela para poder editar la agenda.'
        );

        $session->delete();

        return redirect()
            ->route('executions.show', [
                $execution,
                'tab' => 'sessions',
            ])
            ->with(
                'status',
                'Sesión eliminada correctamente.'
            );
    }
}