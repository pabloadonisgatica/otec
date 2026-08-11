<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AppSetting;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class SettingsController extends Controller
{
    public function index()
    {
        $breadcrumbs = [
            ['label' => 'Configuración', 'url' => route('settings.index')],
        ];

        $users = User::query()
            ->orderBy('name')
            ->paginate(10);

        return view('settings.index', compact('breadcrumbs', 'users'));
    }
    public function updateLogo(Request $request)
    {
        $request->validate([
            'logo' => ['required', 'file', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
        ]);

        // si ya había logo, lo borramos
        $old = AppSetting::get('app_logo');
        if ($old && Storage::disk('public')->exists($old)) {
            Storage::disk('public')->delete($old);
        }

        // guardar nuevo logo en storage/app/public/logos
        $path = $request->file('logo')->store('logos', 'public');

        // guardar setting
        AppSetting::updateOrCreate(
            ['key' => 'app_logo'],
            ['value' => $path]
        );

        return back()->with('status', 'Logo actualizado correctamente.');
    }

    public function updateOtecName(Request $request)
    {
        $request->validate([
            'otec_name' => ['required', 'string', 'max:255'],
        ]);

        AppSetting::updateOrCreate(
            ['key' => 'otec_name'],
            ['value' => $request->string('otec_name')]
        );

        return back()->with('status', 'Nombre de la OTEC actualizado correctamente.');
    }

}
