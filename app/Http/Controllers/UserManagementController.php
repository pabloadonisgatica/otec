<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
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
        ]);

        // si es admin, no necesitamos guardar permisos
        if (($data['role'] ?? 'user') === 'admin') {
            $data['permissions'] = null;
        } else {
            $data['permissions'] = array_values(array_intersect(array_keys($this->modules), $data['permissions'] ?? []));
        }

        $user = User::create($data);

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

        $user->update($data);

        return back()->with('status', 'Usuario actualizado correctamente.');
    }
}
