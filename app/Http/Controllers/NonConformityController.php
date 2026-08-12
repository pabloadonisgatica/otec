<?php

namespace App\Http\Controllers;

use App\Models\Execution;
use App\Models\NonConformity;
use App\Models\User;
use Illuminate\Http\Request;

class NonConformityController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->string('status')->toString();
        $code = $request->string('code')->toString();
        $from = $request->date('from');
        $to = $request->date('to');

        $nonConformities = NonConformity::query()
            ->with(['execution:id,internal_code,course_name', 'responsible:id,name', 'actions'])
            ->when($status, fn ($query) => $query->where('status', $status))
            ->when($code, fn ($query) => $query->where('code', 'like', "%{$code}%"))
            ->when($from, fn ($query) => $query->whereDate('detected_at', '>=', $from))
            ->when($to, fn ($query) => $query->whereDate('detected_at', '<=', $to))
            ->withCount([
                'actions',
                'actions as pending_actions_count' => fn ($query) => $query->where('status', '!=', 'completed'),
            ])
            ->latest('detected_at')
            ->paginate(20)
            ->withQueryString();

        return view('quality.non-conformities.index', [
            'nonConformities' => $nonConformities,
            'status' => $status,
            'code' => $code,
            'from' => $request->string('from')->toString(),
            'to' => $request->string('to')->toString(),
        ]);
    }

    public function create()
    {
        $executions = Execution::orderByDesc('start_date')->get(['id', 'internal_code', 'course_name']);
        $users = User::orderBy('name')->get(['id', 'name']);

        return view('quality.non-conformities.create', compact('executions', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'source' => ['nullable', 'in:internal_audit,external_audit,complaint,survey,other'],
            'process' => ['nullable', 'string', 'max:255'],
            'origin' => ['nullable', 'string', 'max:255'],
            'nc_type' => ['nullable', 'in:major,minor,observation'],
            'objective_evidence' => ['nullable', 'string'],
            'normative_reference' => ['nullable', 'string', 'max:255'],
            'correction' => ['nullable', 'string'],
            'root_cause' => ['nullable', 'string'],
            'execution_id' => ['nullable', 'exists:executions,id'],
            'responsible_id' => ['nullable', 'exists:users,id'],
            'detected_at' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $nonConformity = NonConformity::create($validated);

        return redirect()
            ->route('quality.non-conformities.show', $nonConformity)
            ->with('status', 'No conformidad registrada correctamente.');
    }

    public function show(NonConformity $non_conformity)
    {
        $non_conformity->load(['execution', 'responsible', 'actions.responsible']);

        $users = User::orderBy('name')->get(['id', 'name']);

        return view('quality.non-conformities.show', [
            'nonConformity' => $non_conformity,
            'users' => $users,
        ]);
    }

    public function edit(NonConformity $non_conformity)
    {
        $executions = Execution::orderByDesc('start_date')->get(['id', 'internal_code', 'course_name']);
        $users = User::orderBy('name')->get(['id', 'name']);

        return view('quality.non-conformities.edit', [
            'nonConformity' => $non_conformity,
            'executions' => $executions,
            'users' => $users,
        ]);
    }

    public function update(Request $request, NonConformity $non_conformity)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'source' => ['nullable', 'in:internal_audit,external_audit,complaint,survey,other'],
            'process' => ['nullable', 'string', 'max:255'],
            'origin' => ['nullable', 'string', 'max:255'],
            'nc_type' => ['nullable', 'in:major,minor,observation'],
            'objective_evidence' => ['nullable', 'string'],
            'normative_reference' => ['nullable', 'string', 'max:255'],
            'correction' => ['nullable', 'string'],
            'root_cause' => ['nullable', 'string'],
            'execution_id' => ['nullable', 'exists:executions,id'],
            'responsible_id' => ['nullable', 'exists:users,id'],
            'detected_at' => ['required', 'date'],
            'status' => ['required', 'in:open,in_progress,closed'],
            'notes' => ['nullable', 'string'],
        ]);

        $wasClosed = $non_conformity->isClosed();
        $willBeClosed = $validated['status'] === 'closed';

        if (! $wasClosed && $willBeClosed) {
            $validated['closed_at'] = now();
        } elseif (! $willBeClosed) {
            $validated['closed_at'] = null;
        }

        $non_conformity->update($validated);

        return redirect()
            ->route('quality.non-conformities.show', $non_conformity)
            ->with('status', 'No conformidad actualizada correctamente.');
    }

    public function destroy(NonConformity $non_conformity)
    {
        $non_conformity->delete();

        return redirect()
            ->route('quality.non-conformities.index')
            ->with('status', 'No conformidad eliminada.');
    }
}
