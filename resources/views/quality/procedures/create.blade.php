<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Crear Procedimiento</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <x-breadcrumb :items="[
                ['label' => 'Ver Norma', 'url' => route('quality.norm.index')],
                ['label' => 'Procedimientos', 'url' => route('quality.procedures.index')],
                ['label' => 'Crear'],
            ]" />

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden p-6">

                <form method="POST" action="{{ route('quality.procedures.store') }}"
                      enctype="multipart/form-data" class="space-y-6" x-data="{ documentType: 'file' }">
                    @csrf

                    <div>
                        <h3 class="font-semibold text-gray-900 mb-4">1. Información del documento</h3>

                        <div class="space-y-4">

                            <div>
                                <label class="block text-sm font-medium">Título</label>
                                <input type="text" name="name" value="{{ old('name') }}" required
                                       placeholder="Ej: P-N-03 Planificación y Ejecución del Servicio"
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
                                        Enlace (Google Drive, etc.)
                                    </label>
                                </div>

                                <div x-show="documentType === 'file'">
                                    <input type="file" name="file" class="w-full text-sm">
                                    @error('file') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                                    <p class="text-xs text-gray-400 mt-1">Peso máximo: 70 MB.</p>
                                </div>

                                <div x-show="documentType === 'link'" x-cloak>
                                    <input type="url" name="external_url" value="{{ old('external_url') }}"
                                           placeholder="https://drive.google.com/..."
                                           class="w-full rounded-md border-gray-300 text-sm">
                                    @error('external_url') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium">Detalle</label>
                                <textarea name="description" rows="3"
                                          class="mt-1 w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('description') }}</textarea>
                                @error('description') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>

                        </div>
                    </div>

                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="font-semibold text-gray-900 mb-4">2. Control y aprobación</h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium">Encargado Revisión</label>
                                <input type="text" name="reviewer_name" value="{{ old('reviewer_name') }}"
                                       class="mt-1 w-full rounded-md border-gray-300 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium">Fecha Revisión</label>
                                <input type="date" name="review_date" value="{{ old('review_date') }}"
                                       class="mt-1 w-full rounded-md border-gray-300 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium">Encargado Aprobación</label>
                                <input type="text" name="approver_name" value="{{ old('approver_name') }}"
                                       class="mt-1 w-full rounded-md border-gray-300 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium">Fecha Aprobación</label>
                                <input type="date" name="approval_date" value="{{ old('approval_date') }}"
                                       class="mt-1 w-full rounded-md border-gray-300 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium">Fecha Próxima Revisión</label>
                                <input type="date" name="next_review_date" value="{{ old('next_review_date') }}"
                                       class="mt-1 w-full rounded-md border-gray-300 text-sm">
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="block text-sm font-medium">Observación</label>
                            <textarea name="observation" rows="2"
                                      class="mt-1 w-full rounded-md border-gray-300 text-sm">{{ old('observation') }}</textarea>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 pt-2">
                        <button class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md text-xs font-semibold uppercase tracking-widest hover:bg-indigo-500">
                            Crear
                        </button>
                        <a href="{{ route('quality.procedures.index') }}" class="text-sm text-gray-600 hover:underline">Volver</a>
                    </div>

                </form>

            </div>
        </div>
    </div>
</x-app-layout>
