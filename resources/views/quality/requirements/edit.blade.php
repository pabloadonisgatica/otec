<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar Requerimiento</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <x-breadcrumb :items="[
                ['label' => 'Ver Norma', 'url' => route('quality.norm.index')],
                ['label' => 'Requerimientos del Cliente', 'url' => route('quality.requirements.index')],
                ['label' => $requirement->name, 'url' => route('quality.requirements.show', $requirement)],
                ['label' => 'Editar'],
            ]" />

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden p-6">

                <form method="POST" action="{{ route('quality.requirements.update', $requirement) }}" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-medium">Título</label>
                        <input type="text" name="name" value="{{ old('name', $requirement->name) }}" required
                               class="mt-1 w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        @error('name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center gap-3 pt-2">
                        <button class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md text-xs font-semibold uppercase tracking-widest hover:bg-indigo-500">
                            Guardar
                        </button>
                        <a href="{{ route('quality.requirements.show', $requirement) }}" class="text-sm text-gray-600 hover:underline">Volver</a>
                    </div>

                </form>

                <form method="POST" action="{{ route('quality.requirements.destroy', $requirement) }}"
                      class="mt-6 pt-6 border-t border-gray-200"
                      onsubmit="return confirm('¿Eliminar este requerimiento? Se eliminarán todas sus versiones.')">
                    @csrf
                    @method('DELETE')
                    <button class="text-sm text-red-600 hover:underline">
                        Eliminar requerimiento
                    </button>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
