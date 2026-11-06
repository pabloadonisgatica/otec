<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Usuarios
            </h2>

            <a href="{{ route('settings.users.create') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                Nuevo usuario
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @isset($breadcrumbs)
                <x-breadcrumb :items="$breadcrumbs" />
            @endisset

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6">

                    <div class="overflow-x-auto">
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
                                        <td class="py-3 pr-4 font-medium text-gray-800">
                                            {{ $u->name }}
                                        </td>
                                        <td class="py-3 pr-4 text-gray-700">
                                            {{ $u->email }}
                                        </td>
                                        <td class="py-3 pr-4">
                                            <span class="inline-flex px-2 py-1 rounded text-xs font-semibold
                                                {{ $u->role === 'admin' ? 'bg-indigo-50 text-indigo-700' : 'bg-blue-50 text-blue-700' }}">
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
</x-app-layout>
