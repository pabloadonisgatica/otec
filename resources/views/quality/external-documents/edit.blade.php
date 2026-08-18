<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar Documento Externo</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <x-breadcrumb :items="[
                ['label' => 'Ver Norma', 'url' => route('quality.norm.index')],
                ['label' => 'Documentos Externos', 'url' => route('quality.external-documents.index')],
                ['label' => $document->name, 'url' => route('quality.external-documents.show', $document)],
                ['label' => 'Editar'],
            ]" />

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden p-6">

                <form method="POST" action="{{ route('quality.external-documents.update', $document) }}" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-medium">Título del documento</label>
                        <input type="text" name="name" value="{{ old('name', $document->name) }}" required
                               class="mt-1 w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        @error('name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Fecha Próxima Revisión</label>
                        <input type="date" name="next_review_date" value="{{ old('next_review_date', optional($document->next_review_date)->format('Y-m-d')) }}"
                               class="mt-1 w-full rounded-md border-gray-300 text-sm">
                    </div>

                    <div class="flex items-center gap-3 pt-2">
                        <button class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md text-xs font-semibold uppercase tracking-widest hover:bg-indigo-500">
                            Guardar
                        </button>
                        <a href="{{ route('quality.external-documents.show', $document) }}" class="text-sm text-gray-600 hover:underline">Volver</a>
                    </div>

                </form>

                <form method="POST" action="{{ route('quality.external-documents.destroy', $document) }}"
                      class="mt-6 pt-6 border-t border-gray-200"
                      onsubmit="return confirm('¿Eliminar este documento externo? Se eliminarán todas sus versiones.')">
                    @csrf
                    @method('DELETE')
                    <button class="text-sm text-red-600 hover:underline">
                        Eliminar documento
                    </button>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
