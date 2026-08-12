<?php

namespace App\Http\Controllers;

use App\Models\Provider;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ProviderController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        $providers = Provider::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('rut', 'like', "%{$q}%")
                        ->orWhere('name', 'like', "%{$q}%")
                        ->orWhere('business_name', 'like', "%{$q}%")
                        ->orWhere('category', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%")
                        ->orWhere('contact_name', 'like', "%{$q}%");
                });
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('quality.providers.index', compact('providers', 'q'));
    }

    public function create()
    {
        return view('quality.providers.create');
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        $provider = Provider::create($data);

        return redirect()
            ->route('quality.providers.show', $provider)
            ->with('status', 'Proveedor registrado correctamente.');
    }

    public function show(Provider $provider)
    {
        return view('quality.providers.show', compact('provider'));
    }

    public function edit(Provider $provider)
    {
        return view('quality.providers.edit', compact('provider'));
    }

    public function update(Request $request, Provider $provider)
    {
        $data = $this->validateData($request);

        $provider->update($data);

        return redirect()
            ->route('quality.providers.show', $provider)
            ->with('status', 'Proveedor actualizado correctamente.');
    }

    public function destroy(Provider $provider)
    {
        $provider->delete();

        return redirect()
            ->route('quality.providers.index')
            ->with('status', 'Proveedor eliminado.');
    }

    /**
     * Informe descargable del proveedor (PDF).
     */
    public function report(Provider $provider)
    {
        $otecName = \App\Models\AppSetting::get('otec_name', 'Nombre de la OTEC no configurado');

        $html = view('quality.providers.report-pdf', compact('provider', 'otecName'))->render();

        $pdf = Pdf::loadHTML($html)->setPaper('letter', 'portrait');

        return $pdf->stream('informe-proveedor-' . $provider->id . '.pdf');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'rut' => ['nullable', 'string', 'max:20'],
            'name' => ['required', 'string', 'max:255'],
            'business_name' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:255'],
            'region' => ['nullable', 'string', 'max:255'],
            'commune' => ['nullable', 'string', 'max:255'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string'],
        ]);
    }
}
