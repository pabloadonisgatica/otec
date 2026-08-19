<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Planificación Estratégica</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <x-breadcrumb :items="[
                ['label' => 'Ver Norma', 'url' => route('quality.norm.index')],
                ['label' => 'Planificación Estratégica'],
            ]" />

            @if (session('status'))
                <div class="mb-4 p-3 rounded bg-green-50 text-green-700">
                    {{ session('status') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Documento --}}
                <div class="lg:col-span-2 space-y-6">

                    <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden p-6">

                        <h3 class="font-semibold text-gray-900 mb-4">Documento</h3>

                        @php
                            $current = $document->versions->first();
                        @endphp

                        <x-document-preview :version="$current" />

                        <div class="mt-6">

                            <h4 class="text-sm font-semibold text-gray-700 mb-3">Subir nueva versión</h4>

                            <form method="POST" action="{{ route('quality.strategic-plan.versions.upload') }}"
                                  enctype="multipart/form-data" class="space-y-3" x-data="{ documentType: 'file' }">
                                @csrf

                                <div>
                                    <label class="block text-xs text-gray-500">Título del documento</label>
                                    <input type="text" name="title" value="{{ old('title', $document->name) }}"
                                           class="mt-1 w-full text-sm rounded-md border-gray-300">
                                </div>

                                <div class="flex gap-2">
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
                                    @error('file') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div x-show="documentType === 'link'" x-cloak>
                                    <input type="url" name="external_url" placeholder="https://..."
                                           class="w-full rounded-md border-gray-300 text-sm">
                                    @error('external_url') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div class="flex justify-end">
                                    <button class="px-4 py-2 rounded-md bg-teal-600 text-white text-xs font-semibold uppercase tracking-widest hover:bg-teal-700">
                                        Subir versión
                                    </button>
                                </div>

                            </form>

                        </div>

                        @if($document->versions->count() > 1)
                            <div class="mt-6 border-t border-gray-200 pt-4">
                                <p class="text-sm font-semibold text-gray-700 mb-3">Historial</p>
                                <div class="space-y-2">
                                    @foreach($document->versions->skip(1) as $version)
                                        <div class="flex items-center justify-between text-sm">
                                            <span class="text-gray-600">
                                                v{{ $version->version_number }} — {{ $version->isExternalLink() ? 'Enlace externo' : $version->file_name }}
                                                <span class="text-xs text-gray-400">({{ $version->uploaded_at->format('d-m-Y') }})</span>
                                            </span>
                                            <a href="{{ route('quality.documents.versions.download', $version) }}" target="_blank"
                                               class="text-indigo-600 hover:underline text-xs font-medium">
                                                {{ $version->isExternalLink() ? 'Abrir' : 'Descargar' }}
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                    </div>

                </div>

                {{-- Indicadores --}}
                <div class="lg:col-span-1">

                    <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden p-6">

                        <h3 class="font-semibold text-gray-900 mb-4">Indicadores</h3>

                        <div class="space-y-3 mb-4">

                            @forelse($document->indicators as $indicator)

                                <div>
                                    <label class="block text-xs text-gray-500 uppercase">{{ $indicator->name }}</label>

                                    <form method="POST" action="{{ route('quality.strategic-plan.indicators.update', $indicator) }}"
                                          class="flex gap-1 mt-1">
                                        @csrf
                                        @method('PUT')
                                        <input type="text" name="value" value="{{ $indicator->value }}"
                                               class="flex-1 text-sm rounded-md border-gray-300">
                                        <button class="px-2 py-1 rounded bg-teal-600 text-white text-xs">Grabar</button>
                                    </form>

                                    <form method="POST" action="{{ route('quality.strategic-plan.indicators.destroy', $indicator) }}"
                                          onsubmit="return confirm('¿Eliminar este indicador?')" class="mt-1">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-xs text-red-600 hover:underline">Eliminar</button>
                                    </form>
                                </div>

                            @empty

                                <p class="text-sm text-gray-500">Sin indicadores aún.</p>

                            @endforelse

                        </div>

                        <div class="border-t border-gray-100 pt-4">
                            <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Nuevo indicador</p>

                            <form method="POST" action="{{ route('quality.strategic-plan.indicators.store') }}" class="space-y-2">
                                @csrf
                                <input type="text" name="name" required placeholder="Nombre (ej: Ventas Proyectadas)"
                                       class="w-full text-sm rounded-md border-gray-300">
                                <input type="text" name="value" required placeholder="Valor"
                                       class="w-full text-sm rounded-md border-gray-300">
                                <button class="w-full px-3 py-1.5 rounded-md bg-indigo-600 text-white text-xs font-semibold">
                                    + Agregar indicador
                                </button>
                            </form>
                        </div>

                    </div>

                </div>

            </div>

        </div>
    </div>
</x-app-layout>
