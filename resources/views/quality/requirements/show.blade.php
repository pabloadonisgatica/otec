<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $requirement->name }}</h2>

            <a href="{{ route('quality.requirements.edit', $requirement) }}"
               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-md text-xs font-semibold uppercase tracking-widest hover:bg-gray-50">
                Editar
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <x-breadcrumb :items="[
                ['label' => 'Ver Norma', 'url' => route('quality.norm.index')],
                ['label' => 'Requerimientos del Cliente', 'url' => route('quality.requirements.index')],
                ['label' => $requirement->name],
            ]" />

            @if (session('status'))
                <div class="mb-4 p-3 rounded bg-green-50 text-green-700">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden p-6">

                <h3 class="font-semibold text-gray-900 mb-4">Documento vigente</h3>

                @php
                    $current = $requirement->versions->first();
                @endphp

                <x-document-preview :version="$current" />

                <div class="mb-4"></div>

                <h4 class="text-sm font-semibold text-gray-700 mb-3">Subir nueva versión</h4>

                <form method="POST" action="{{ route('quality.requirements.versions.upload', $requirement) }}"
                      enctype="multipart/form-data" class="space-y-3" x-data="{ documentType: 'file' }">
                    @csrf

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

                @if($requirement->versions->count() > 1)
                    <div class="mt-8 border-t border-gray-200 pt-6">

                        <h3 class="font-semibold text-gray-900 mb-4">Historial</h3>

                        <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-left border-b bg-gray-50">
                                    <th class="py-2 px-3">Versión</th>
                                    <th class="py-2 px-3">Documento</th>
                                    <th class="py-2 px-3">Subido por</th>
                                    <th class="py-2 px-3">Fecha</th>
                                    <th class="py-2 px-3"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($requirement->versions->skip(1) as $version)
                                    <tr class="border-b">
                                        <td class="py-2 px-3">v{{ $version->version_number }}</td>
                                        <td class="py-2 px-3">{{ $version->isExternalLink() ? 'Enlace externo' : $version->file_name }}</td>
                                        <td class="py-2 px-3 text-gray-600">{{ $version->uploaded_by }}</td>
                                        <td class="py-2 px-3 text-gray-600 whitespace-nowrap">{{ $version->uploaded_at->format('d-m-Y H:i') }}</td>
                                        <td class="py-2 px-3 text-right">
                                            <a href="{{ route('quality.documents.versions.download', $version) }}" target="_blank"
                                               class="text-indigo-600 hover:underline text-xs font-medium">
                                                {{ $version->isExternalLink() ? 'Abrir' : 'Descargar' }}
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        </div>

                    </div>
                @endif

            </div>

        </div>
    </div>
</x-app-layout>
