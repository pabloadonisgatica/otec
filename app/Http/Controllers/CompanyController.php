<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    /**
     * Listado de empresas + buscador
     */
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        $companies = Company::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('rut', 'like', "%{$q}%")
                        ->orWhere('name', 'like', "%{$q}%")
                        ->orWhere('business_name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%")
                        ->orWhere('contact_name', 'like', "%{$q}%")
                        ->orWhere('phone', 'like', "%{$q}%")
                        ->orWhere('address', 'like', "%{$q}%")
                        ->orWhere('region', 'like', "%{$q}%")
                        ->orWhere('commune', 'like', "%{$q}%");
                });
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $breadcrumbs = [
            ['label' => 'Empresas'],
        ];

        return view('companies.index', compact('companies', 'q', 'breadcrumbs'));
    }

    /**
     * Formulario crear empresa
     */
    public function create()
    {
        $breadcrumbs = [
            ['label' => 'Empresas', 'url' => route('companies.index')],
            ['label' => 'Nueva empresa'],
        ];

        return view('companies.create', compact('breadcrumbs'));
    }

    /**
     * Guardar empresa
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'rut' => ['required', 'string', 'max:20', 'unique:companies,rut'],
            'name' => ['required', 'string', 'max:255'],
            'business_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:255'],
            'region' => ['nullable', 'string', 'max:255'],
            'commune' => ['nullable', 'string', 'max:255'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
        ]);

        Company::create($data);

        return redirect()
            ->route('companies.index')
            ->with('status', 'Empresa creada correctamente.');
    }

    /**
     * Formulario editar empresa
     */
    public function edit(Company $company)
    {
        $breadcrumbs = [
            ['label' => 'Empresas', 'url' => route('companies.index')],
            ['label' => 'Editar empresa'],
        ];

        return view('companies.edit', compact('company', 'breadcrumbs'));
    }

    /**
     * Actualizar empresa
     */
    public function update(Request $request, Company $company)
    {
        $data = $request->validate([
            'rut' => ['required', 'string', 'max:20', 'unique:companies,rut,' . $company->id],
            'name' => ['required', 'string', 'max:255'],
            'business_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:255'],
            'region' => ['nullable', 'string', 'max:255'],
            'commune' => ['nullable', 'string', 'max:255'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
        ]);

        $company->update($data);

        return redirect()
            ->route('companies.index')
            ->with('status', 'Empresa actualizada correctamente.');
    }

    /**
     * Eliminar empresa
     */
    public function destroy(Company $company)
    {
        $company->delete();

        return redirect()
            ->route('companies.index')
            ->with('status', 'Empresa eliminada correctamente.');
    }

    /**
     * Detalle empresa (JSON para modal)
     */
    public function show(Company $company)
    {
        return response()->json($company);
    }
}
