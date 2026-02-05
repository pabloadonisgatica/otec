<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Configuración
            </h2>
        </div>
    </x-slot>

    <div class="py-6" x-data="{ tab: 'logo' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @isset($breadcrumbs)
                <x-breadcrumb :items="$breadcrumbs" />
            @endisset

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6">

                    {{-- Tabs --}}
                    <div class="border-b border-gray-200 mb-6">
                        <nav class="-mb-px flex gap-6">
                            <button type="button"
                                    class="py-2 px-1 border-b-2 text-sm font-medium"
                                    :class="tab === 'logo' ? 'border-gray-800 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                    @click="tab = 'logo'">
                                Información OTEC (Logo)
                            </button>

                            <button type="button"
                                    class="py-2 px-1 border-b-2 text-sm font-medium"
                                    :class="tab === 'users' ? 'border-gray-800 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                    @click="tab = 'users'">
                                Gestión de usuarios
                            </button>
                        </nav>
                    </div>

                    {{-- TAB: Logo --}}
                    <div x-show="tab === 'logo'" x-cloak>
                        <h3 class="text-lg font-semibold text-gray-800">Logo OTEC</h3>
                        <p class="text-sm text-gray-500 mt-1">
                            Sube el logo que se mostrará en la navegación del sistema.
                        </p>

                        @if (session('status'))
                            <div class="mt-4 p-3 rounded bg-green-50 text-green-700 text-sm">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form class="mt-6 space-y-4" action="{{ route('settings.logo.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Subir logo</label>
                                <input type="file" name="logo"
                                       class="mt-2 block w-full text-sm text-gray-700
                                              file:mr-4 file:py-2 file:px-4
                                              file:rounded-md file:border-0
                                              file:text-sm file:font-semibold
                                              file:bg-gray-800 file:text-white
                                              hover:file:bg-gray-700" />
                                @error('logo')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-2 text-xs text-gray-500">PNG/JPG/WEBP, máximo 2MB.</p>
                            </div>

                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                Guardar logo
                            </button>
                        </form>
                    </div>

                    {{-- TAB: Usuarios --}}
                    <div x-show="tab === 'users'" x-cloak>
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">Usuarios</h3>
                                <p class="text-sm text-gray-500 mt-1">
                                    Administra usuarios, roles y permisos por módulo.
                                </p>
                            </div>

                            <a href="{{ route('settings.users.create') }}"
                               class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                Nuevo usuario
                            </a>
                        </div>

                        <div class="mt-6 overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="text-left text-gray-600 border-b">
                                    <tr>
                                        <th class="py-2 pr-4">Nombre</th>
                                        <th class="py-2 pr-4">Email</th>
                                        <th class="py-2 pr-4">Rol</th>
                                        <th class="py-2 pr-4">Permisos</th>
                                        <th class="py-2 text-right">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y">
                                    @forelse($users as $u)
                                        <tr>
                                            <td class="py-3 pr-4 font-medium text-gray-800">{{ $u->name }}</td>
                                            <td class="py-3 pr-4 text-gray-700">{{ $u->email }}</td>
                                            <td class="py-3 pr-4">
                                                <span class="inline-flex px-2 py-1 rounded text-xs font-semibold
                                                    {{ $u->role === 'admin' ? 'bg-indigo-50 text-indigo-700' : 'bg-gray-100 text-gray-700' }}">
                                                    {{ $u->role === 'admin' ? 'Administrador' : 'Usuario' }}
                                                </span>
                                            </td>
                                            <td class="py-3 pr-4 text-gray-600">
                                                @if($u->role === 'admin')
                                                    Todos
                                                @else
                                                    {{ $u->permissions ? implode(', ', $u->permissions) : '-' }}
                                                @endif
                                            </td>
                                            <td class="py-3 text-right">
                                                <a href="{{ route('settings.users.edit', $u) }}"
                                                   class="text-gray-800 hover:underline">
                                                    Editar
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="py-6 text-center text-gray-500">
                                                No hay usuarios registrados.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $users->links() }}
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
