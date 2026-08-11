<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Plantillas de Diplomas</h2>

            <div class="flex gap-2">
                <a href="{{ route('diplomas.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-md text-xs font-semibold uppercase tracking-widest hover:bg-gray-50">
                    Diplomas emitidos
                </a>

                <a href="{{ route('diploma-templates.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md text-xs font-semibold uppercase tracking-widest hover:bg-indigo-500">
                    Nueva plantilla
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('status'))
                <div class="mb-4 p-3 rounded bg-green-50 text-green-700">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left border-b">
                                <th class="py-2 pr-4">Nombre</th>
                                <th class="py-2 pr-4">Activa</th>
                                <th class="py-2 pr-4">Creada</th>
                                <th class="py-2 pr-4"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($templates as $t)
                                <tr class="border-b">
                                    <td class="py-2 pr-4 font-medium">{{ $t->name }}</td>
                                    <td class="py-2 pr-4">
                                        <span class="px-2 py-1 rounded text-xs {{ $t->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                            {{ $t->is_active ? 'Sí' : 'No' }}
                                        </span>
                                    </td>
                                    <td class="py-2 pr-4 text-gray-600">{{ $t->created_at?->format('Y-m-d') }}</td>
                                    <td class="py-2 pr-4 text-right whitespace-nowrap">
                                        <a href="{{ route('diploma-templates.edit', $t) }}"
                                           class="text-indigo-600 hover:underline mr-3">Editar</a>

                                        <form action="{{ route('diploma-templates.destroy', $t) }}" method="POST" class="inline"
                                              onsubmit="return confirm('¿Eliminar plantilla?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-red-600 hover:underline">Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-6 text-center text-gray-500">
                                        No hay plantillas aún.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $templates->links() }}
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
