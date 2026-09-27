<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDiplomaLogoRequest;
use App\Models\DiplomaLogo;
use Illuminate\Support\Str;

/**
 * Biblioteca de logos adicionales (logo 2) para diplomas.
 * Se administra desde Configuración → Diplomas.
 */
class DiplomaLogoController extends Controller
{
    public function store(StoreDiplomaLogoRequest $request)
    {
        $file = $request->file('logo_file');

        DiplomaLogo::create([
            // Sin nombre escrito, se usa el del archivo: "paccar-logo.png" → "Paccar logo".
            'name' => $request->validated('logo_name')
                ?: Str::ucfirst(str_replace(['-', '_'], ' ', pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))),
            'path' => $file->store('diplomas/logos', 'public'),
        ]);

        return $this->backToDiplomasTab('Logo agregado correctamente.');
    }

    /**
     * Solo lo quita de la lista (soft delete). El archivo se conserva
     * porque los diplomas ya emitidos pueden seguir usándolo.
     */
    public function destroy(DiplomaLogo $diplomaLogo)
    {
        $diplomaLogo->delete();

        return $this->backToDiplomasTab('Logo quitado de la lista.');
    }

    private function backToDiplomasTab(string $message)
    {
        return redirect()
            ->route('settings.index')
            ->with('settings_tab', 'diplomas')
            ->with('settings_subtab', 'logos')
            ->with('status', $message);
    }
}
