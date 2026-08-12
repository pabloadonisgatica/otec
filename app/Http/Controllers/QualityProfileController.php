<?php

namespace App\Http\Controllers;

use App\Models\QualityProfile;
use Illuminate\Http\Request;

class QualityProfileController extends Controller
{
    public function edit()
    {
        $profile = QualityProfile::current();

        return view('quality.profile', compact('profile'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'vision' => ['nullable', 'string'],
            'mission' => ['nullable', 'string'],
            'scope' => ['nullable', 'string'],
            'last_audit_date' => ['nullable', 'date'],
            'legal_documentation' => ['nullable', 'string'],
        ]);

        QualityProfile::current()->update($validated);

        return redirect()
            ->route('quality.profile.edit')
            ->with('status', 'Requisitos generales actualizados correctamente.');
    }
}
