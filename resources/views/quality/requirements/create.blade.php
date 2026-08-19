<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Crear Requerimiento del Cliente</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <x-breadcrumb :items="[
                ['label' => 'Ver Norma', 'url' => route('quality.norm.index')],
                ['label' => 'Requerimientos del Cliente', 'url' => route('quality.requirements.index')],
                ['label' => 'Crear'],
            ]" />

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden p-6">

                <form method="POST" action="{{ route('quality.requirements.store') }}"
                      enctype="multipart/form-data" class="space-y-5" x-data="{ documentType: 'file' }">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium">Título</label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                               placeholder="Ej: Requerimiento Municipalidad de Recoleta - Manzanas del Cuidado"
                               class="mt-1 w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        @error('name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Documento</label>

                        <div class="flex gap-2 mt-1 mb-2">
                            <label class="flex items-center gap-2 text-sm">
                                <input type="radio" name="document_type" value="file" x-model="documentType">
                                Adjuntar archivo
                            </label>
                            <label class="flex items-center gap-2 text-sm ml-4">
                                <input type="radio" name="document_type" value="link" x-model="documentType">
                                Enlace externo
                            </label>
                        </div>

                        <div x-show="documentType === 'file'">
                            <input type="file" name="file" class="w-full text-sm">
                            @error('file') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                            <p class="text-xs text-gray-400 mt-1">Peso máximo: 70 MB.</p>
                        </div>

                        <div x-show="documentType === 'link'" x-cloak>
                            <input type="url" name="external_url" value="{{ old('external_url') }}"
                                   placeholder="https://..."
                                   class="w-full rounded-md border-gray-300 text-sm">
                            @error('external_url') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="flex items-center gap-3 pt-2">
                        <button class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md text-xs font-semibold uppercase tracking-widest hover:bg-indigo-500">
                            Crear
                        </button>
                        <a href="{{ route('quality.requirements.index') }}" class="text-sm text-gray-600 hover:underline">Volver</a>
                    </div>

                </form>

            </div>
        </div>
    </div>
</x-app-layout>
