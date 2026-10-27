<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Participant;
use Illuminate\Http\Request;

class ParticipantController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q'));

        $participants = Participant::query()
            ->with('company')
            ->when($q, function ($query) use ($q) {
                $query->where('rut', 'like', "%{$q}%")
                    ->orWhere('first_name', 'like', "%{$q}%")
                    ->orWhere('last_name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%")
                    ->orWhereHas('company', function ($q2) use ($q) {
                        $q2->where('name', 'like', "%{$q}%");
                    });
            })
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->withQueryString();

        // OJO: no incluyas "Inicio" si tu breadcrumb ya lo agrega solo
        $breadcrumbs = [
            ['label' => 'Participantes', 'url' => route('participants.index')],
        ];

        return view('participants.index', compact('participants', 'q', 'breadcrumbs'));
    }

    public function create()
    {
        $companies = Company::orderBy('name')->get(['id', 'name']);

        $breadcrumbs = [
            ['label' => 'Participantes', 'url' => route('participants.index')],
            ['label' => 'Nuevo', 'url' => null],
        ];

        return view('participants.create', compact('companies', 'breadcrumbs'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'rut' => ['required', 'string', 'max:12', 'unique:participants,rut'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'company_id' => ['required', 'exists:companies,id'],
            'status' => ['required', 'in:activo,inactivo'],
            'notes' => ['nullable', 'string'],
        ]);

        Participant::create($data);

        return redirect()
            ->route('participants.index')
            ->with('success', 'Participante creado.');
    }

    public function edit(Participant $participant)
    {
        $companies = Company::orderBy('name')->get(['id', 'name']);

        $breadcrumbs = [
            ['label' => 'Participantes', 'url' => route('participants.index')],
            ['label' => 'Editar', 'url' => null],
        ];

        return view('participants.edit', compact('participant', 'companies', 'breadcrumbs'));
    }

    public function update(Request $request, Participant $participant)
    {
        $data = $request->validate([
            'rut' => ['required', 'string', 'max:12', 'unique:participants,rut,' . $participant->id],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'company_id' => ['required', 'exists:companies,id'],
            'status' => ['required', 'in:activo,inactivo'],
            'notes' => ['nullable', 'string'],
        ]);

        $participant->update($data);

        return redirect()
            ->route('participants.index')
            ->with('success', 'Participante actualizado.');
    }

    public function destroy(Participant $participant)
    {
        $participant->delete();

        return redirect()
            ->route('participants.index')
            ->with('success', 'Participante eliminado.');
    }

    // ✅ JSON para modal (igual patrón que relatores)
    public function json(Participant $participant)
    {
        $participant->load('company');

        return response()->json([
            'id' => $participant->id,
            'rut' => $participant->rut,
            'name' => $participant->first_name . ' ' . $participant->last_name,
            'email' => $participant->email,
            'phone' => $participant->phone,
            'status' => $participant->status,
            'company' => $participant->company?->name,
            'notes' => $participant->notes,
        ]);
    }
}
