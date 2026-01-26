<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

public function importForm()
{
    $companies = Company::orderBy('name')->get(['id', 'name']);

    $breadcrumbs = [
        ['label' => 'Participantes', 'url' => route('participants.index')],
        ['label' => 'Carga masiva', 'url' => null],
    ];

    return view('participants.import', compact('companies', 'breadcrumbs'));
}

public function import(Request $request)
{
    $request->validate([
        'company_id' => ['required', 'exists:companies,id'],
        'file' => ['required', 'file', 'mimes:csv,txt'],
    ]);

    $companyId = (int) $request->company_id;

    $handle = fopen($request->file('file')->getRealPath(), 'r');
    if (!$handle) {
        return back()->withErrors(['file' => 'No se pudo leer el archivo.']);
    }

    $header = fgetcsv($handle);
    if (!$header) {
        return back()->withErrors(['file' => 'El CSV está vacío.']);
    }

    // Esperamos encabezado exacto
    $expected = ['rut','first_name','last_name','email','phone','status'];
    $headerNorm = array_map(fn($h) => trim(strtolower($h)), $header);

    if ($headerNorm !== $expected) {
        return back()->withErrors([
            'file' => 'Encabezado inválido. Debe ser: rut,first_name,last_name,email,phone,status'
        ]);
    }

    $created = 0;
    $skipped = 0;

    DB::beginTransaction();
    try {
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) === 0) continue;

            $data = array_combine($expected, $row);

            $rut = trim((string)($data['rut'] ?? ''));
            $first = trim((string)($data['first_name'] ?? ''));
            $last = trim((string)($data['last_name'] ?? ''));
            $email = trim((string)($data['email'] ?? '')) ?: null;
            $phone = trim((string)($data['phone'] ?? '')) ?: null;
            $status = trim((string)($data['status'] ?? '')) ?: 'activo';

            // mínimos
            if ($rut === '' || $first === '' || $last === '') {
                $skipped++;
                continue;
            }

            // duplicado por RUT
            if (Participant::where('rut', $rut)->exists()) {
                $skipped++;
                continue;
            }

            if (!in_array($status, ['activo','inactivo'], true)) {
                $status = 'activo';
            }

            Participant::create([
                'rut' => $rut,
                'first_name' => $first,
                'last_name' => $last,
                'email' => $email,
                'phone' => $phone,
                'status' => $status,
                'company_id' => $companyId,
            ]);

            $created++;
        }

        DB::commit();
    } catch (\Throwable $e) {
        DB::rollBack();
        throw $e;
    } finally {
        fclose($handle);
    }

    return redirect()
        ->route('participants.index')
        ->with('success', "Carga completada: {$created} creados, {$skipped} omitidos/duplicados.");
}

}
