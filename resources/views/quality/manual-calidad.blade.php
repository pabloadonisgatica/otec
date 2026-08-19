<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Manual de Calidad</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <x-breadcrumb :items="[
                ['label' => 'Ver Norma', 'url' => route('quality.norm.index')],
                ['label' => 'Manual de Calidad'],
            ]" />

            @if (session('status'))
                <div class="mb-4 p-3 rounded bg-green-50 text-green-700">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Datos de control --}}
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden p-6">

                <h3 class="font-semibold text-gray-900 mb-4">Datos de control</h3>

                <form method="POST" action="{{ route('quality.manual-calidad.update') }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs text-gray-500">Encargado Revisión</label>
                            <input type="text" name="reviewer_name" value="{{ old('reviewer_name', $document->reviewer_name) }}"
                                   class="mt-1 w-full text-sm rounded-md border-gray-300">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500">Fecha Revisión</label>
                            <input type="date" name="review_date" value="{{ old('review_date', optional($document->review_date)->format('Y-m-d')) }}"
                                   class="mt-1 w-full text-sm rounded-md border-gray-300">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500">Encargado Aprobación</label>
                            <input type="text" name="approver_name" value="{{ old('approver_name', $document->approver_name) }}"
                                   class="mt-1 w-full text-sm rounded-md border-gray-300">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500">Fecha Aprobación</label>
                            <input type="date" name="approval_date" value="{{ old('approval_date', optional($document->approval_date)->format('Y-m-d')) }}"
                                   class="mt-1 w-full text-sm rounded-md border-gray-300">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500">Fecha Próxima Revisión</label>
                            <input type="date" name="next_review_date" value="{{ old('next_review_date', optional($document->next_review_date)->format('Y-m-d')) }}"
                                   class="mt-1 w-full text-sm rounded-md border-gray-300">
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button class="px-4 py-2 rounded-md bg-indigo-600 text-white text-xs font-semibold uppercase tracking-widest hover:bg-indigo-500">
                            Guardar
                        </button>
                    </div>
                </form>

            </div>

            {{-- Versión vigente --}}
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden p-6 mt-6">

                <h3 class="font-semibold text-gray-900 mb-4">Versión vigente</h3>

                @php
                    $current = $document->versions->first();
                @endphp

                <x-document-preview :version="$current" />

                @if($current && $current->observation)
                    <p class="text-xs text-gray-500 mt-2">{{ $current->observation }}</p>
                @endif

                <div class="mb-6"></div>

                <h4 class="text-sm font-semibold text-gray-700 mb-3">Subir nueva versión</h4>

                <form method="POST" action="{{ route('quality.manual-calidad.versions.upload') }}"
                      enctype="multipart/form-data" class="space-y-3">
                    @csrf

                    <div>
                        <label class="block text-xs text-gray-500">Archivo</label>
                        <input type="file" name="file" required class="mt-1 w-full text-sm">
                        @error('file') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        <p class="text-xs text-gray-400 mt-1">
                            La versión anterior no se borra — queda guardada en el historial. Peso máximo: 70 MB.
                        </p>
                    </div>

                    <div>
                        <label class="block text-xs text-gray-500">Observación (opcional)</label>
                        <textarea name="observation" rows="2" class="mt-1 w-full text-sm rounded-md border-gray-300"
                                  placeholder="Ej: Actualización numeral 7.5 tras auditoría interna"></textarea>
                    </div>

                    <div class="flex justify-end">
                        <button class="px-4 py-2 rounded-md bg-teal-600 text-white text-xs font-semibold uppercase tracking-widest hover:bg-teal-700">
                            Subir versión
                        </button>
                    </div>
                </form>

            </div>

            {{-- Historial --}}
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden p-6 mt-6">

                <h3 class="font-semibold text-gray-900 mb-4">Historial de versiones</h3>

                @if($document->versions->count() <= 1)

                    <p class="text-sm text-gray-500">Todavía no hay versiones anteriores.</p>

                @else

                    <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left border-b bg-gray-50">
                                <th class="py-2 px-3">Versión</th>
                                <th class="py-2 px-3">Archivo</th>
                                <th class="py-2 px-3">Subido por</th>
                                <th class="py-2 px-3">Fecha</th>
                                <th class="py-2 px-3">Observación</th>
                                <th class="py-2 px-3"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($document->versions->skip(1) as $version)
                                <tr class="border-b">
                                    <td class="py-2 px-3">v{{ $version->version_number }}</td>
                                    <td class="py-2 px-3">{{ $version->file_name }}</td>
                                    <td class="py-2 px-3 text-gray-600">{{ $version->uploaded_by }}</td>
                                    <td class="py-2 px-3 text-gray-600 whitespace-nowrap">{{ $version->uploaded_at->format('d-m-Y H:i') }}</td>
                                    <td class="py-2 px-3 text-gray-600">{{ $version->observation ?? '—' }}</td>
                                    <td class="py-2 px-3 text-right">
                                        <a href="{{ route('quality.documents.versions.download', $version) }}"
                                           class="text-indigo-600 hover:underline text-xs font-medium">
                                            Descargar
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </div>

                @endif

            </div>

        </div>
    </div>
</x-app-layout>
