<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    // módulos disponibles para permisos (checkboxes)
    private array $modules = [
        'companies' => 'Empresas',
        'budgets' => 'Presupuestos',
        'courses' => 'Cursos',
        'instructors' => 'Relatores',
        'participants' => 'Participantes',
        'settings' => 'Configuración',
    ];

    // Sugerencias de cargo — texto libre, no una lista cerrada.
    public const POSITIONS = [
        'Gerente General',
        'Jefe Administrativo',
        'Auditor Interno',
        'Relator',
        'Representante de la Gerencia',
        'Socio',
    ];

    public function index()
    {
        $users = User::query()
            ->orderBy('name')
            ->paginate(15);

        $breadcrumbs = [
            ['label' => 'Configuración', 'url' => route('settings.index')],
            ['label' => 'Usuarios', 'url' => route('settings.users.index')],
        ];

        return view('settings.users.index', [
            'users' => $users,
            'breadcrumbs' => $breadcrumbs,
        ]);
    }

    public function create()
    {
        $breadcrumbs = [
            ['label' => 'Configuración', 'url' => route('settings.index')],
            ['label' => 'Usuarios', 'url' => route('settings.users.index')],
            ['label' => 'Crear', 'url' => route('settings.users.create')],
        ];

        return view('settings.users.create', [
            'breadcrumbs' => $breadcrumbs,
            'modules' => $this->modules,
            'positions' => self::POSITIONS,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', Rule::in(['admin', 'user'])],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string'],
            'position' => ['nullable', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date'],
            'gender' => ['nullable', 'in:Mujer,Hombre,Otro,Prefiero no indicarlo'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ]);

        // si es admin, no necesitamos guardar permisos
        if (($data['role'] ?? 'user') === 'admin') {
            $data['permissions'] = null;
        } else {
            $data['permissions'] = array_values(array_intersect(array_keys($this->modules), $data['permissions'] ?? []));
        }

        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('users/photos', 'public');
        }
        unset($data['photo']);

        $user = User::create($data);

        $this->appendUploadedDocuments($request, $user);

        return redirect()
            ->route('settings.users.edit', $user)
            ->with('status', 'Usuario creado correctamente.');
    }

    public function edit(User $user)
    {
        $breadcrumbs = [
            ['label' => 'Configuración', 'url' => route('settings.index')],
            ['label' => 'Usuarios', 'url' => route('settings.users.index')],
            ['label' => 'Editar', 'url' => route('settings.users.edit', $user)],
        ];

        return view('settings.users.edit', [
            'user' => $user,
            'breadcrumbs' => $breadcrumbs,
            'modules' => $this->modules,
            'positions' => self::POSITIONS,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8'],
            'role' => ['required', Rule::in(['admin', 'user'])],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string'],
            'position' => ['nullable', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date'],
            'gender' => ['nullable', 'in:Mujer,Hombre,Otro,Prefiero no indicarlo'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ]);

        // password solo si viene
        if (empty($data['password'])) {
            unset($data['password']);
        }

        if (($data['role'] ?? 'user') === 'admin') {
            $data['permissions'] = null;
        } else {
            $data['permissions'] = array_values(array_intersect(array_keys($this->modules), $data['permissions'] ?? []));
        }

        if ($request->hasFile('photo')) {
            if ($user->photo_path && Storage::disk('public')->exists($user->photo_path)) {
                Storage::disk('public')->delete($user->photo_path);
            }
            $data['photo_path'] = $request->file('photo')->store('users/photos', 'public');
        }
        unset($data['photo']);

        $user->update($data);

        $this->appendUploadedDocuments($request, $user);

        return back()->with('status', 'Usuario actualizado correctamente.');
    }

    /**
     * Mismo patrón exacto que InstructorController: agrega los
     * documentos subidos al array existente, sin borrar los previos.
     */
    private function appendUploadedDocuments(Request $request, User $user): void
    {
        $existing = $user->documents ?? [];
        $existing = array_values(array_filter($existing, function ($d) {
            return is_array($d) && !empty($d['path'] ?? null);
        }));

        if (!$request->hasFile('documents')) {
            $user->documents = $existing;
            $user->save();
            return;
        }

        $label = trim((string) $request->input('document_label', 'Documento'));
        if ($label === '') $label = 'Documento';

        foreach ($request->file('documents') as $file) {
            if (!$file) continue;

            $safeBase = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
            $ext = $file->getClientOriginalExtension();
            $filename = $safeBase . '_' . Str::random(8) . '.' . $ext;

            $path = $file->storeAs('users/' . $user->id, $filename, 'public');

            $existing[] = [
                'label' => $label,
                'path' => $path,
                'name' => $file->getClientOriginalName(),
                'mime' => $file->getClientMimeType(),
                'size' => $file->getSize(),
                'uploaded_at' => now()->toDateTimeString(),
            ];
        }

        $user->documents = $existing;
        $user->save();
    }
}
