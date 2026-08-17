<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Requisitos Generales</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            @include('quality._nav')

            @if (session('status'))
                <div class="mb-4 p-3 rounded bg-green-50 text-green-700">
                    {{ session('status') }}
                </div>
            @endif

            <div x-data="{ section: '{{ request('tab', 'general') }}' }">

                <div class="flex gap-1 mb-6 border-b border-gray-200 overflow-x-auto">
                    <button type="button" @click="section = 'general'"
                            class="px-4 py-2.5 border-b-2 text-sm whitespace-nowrap"
                            :class="section === 'general' ? 'border-indigo-600 text-indigo-600 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700'">
                        Visión, Misión y Alcance
                    </button>
                    <button type="button" @click="section = 'legal'"
                            class="px-4 py-2.5 border-b-2 text-sm whitespace-nowrap"
                            :class="section === 'legal' ? 'border-indigo-600 text-indigo-600 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700'">
                        Documentación Legal
                    </button>
                    <button type="button" @click="section = 'process-map'"
                            class="px-4 py-2.5 border-b-2 text-sm whitespace-nowrap"
                            :class="section === 'process-map' ? 'border-indigo-600 text-indigo-600 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700'">
                        Mapa de Procesos
                    </button>
                    <button type="button" @click="section = 'org-chart'"
                            class="px-4 py-2.5 border-b-2 text-sm whitespace-nowrap"
                            :class="section === 'org-chart' ? 'border-indigo-600 text-indigo-600 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700'">
                        Organigrama
                    </button>
                </div>

                {{-- Visión, Misión, Alcance, Última Auditoría --}}
                <div x-show="section === 'general'" x-cloak>

                    <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden p-6">

                        <form method="POST" action="{{ route('quality.profile.update') }}" class="space-y-5">
                            @csrf
                            @method('PUT')

                            <div>
                                <label class="block text-sm font-medium">Visión</label>
                                <textarea name="vision" rows="3"
                                          class="mt-1 w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('vision', $profile->vision) }}</textarea>
                                @error('vision') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium">Misión</label>
                                <textarea name="mission" rows="3"
                                          class="mt-1 w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('mission', $profile->mission) }}</textarea>
                                @error('mission') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium">Alcance</label>
                                <textarea name="scope" rows="3"
                                          class="mt-1 w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('scope', $profile->scope) }}</textarea>
                                @error('scope') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium">Fecha de la última auditoría</label>
                                <input type="date" name="last_audit_date"
                                       value="{{ old('last_audit_date', optional($profile->last_audit_date)->format('Y-m-d')) }}"
                                       class="mt-1 w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                @error('last_audit_date') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="pt-2">
                                <button class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md text-xs font-semibold uppercase tracking-widest hover:bg-indigo-500">
                                    Guardar
                                </button>
                            </div>

                        </form>

                    </div>

                </div>

                {{-- Documentación Legal --}}
                <div x-show="section === 'legal'" x-cloak>

                    <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden p-6">

                        <h3 class="font-semibold text-gray-900 mb-4">Documentación Legal</h3>

                        <form method="POST" action="{{ route('quality.profile.documents.upload') }}"
                              enctype="multipart/form-data" class="space-y-4">
                            @csrf

                            <div>
                                <label class="block text-sm font-medium">Título</label>
                                <input type="text" name="title" value="{{ old('title') }}" required
                                       class="mt-1 w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                @error('title') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium">Seleccionar archivo</label>
                                <input type="file" name="file" required class="mt-1 w-full text-sm">
                                @error('file') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                                <p class="text-xs text-gray-400 mt-1">
                                    ⚠️ La carga puede demorar según el peso del archivo. Peso máximo: 70 MB.
                                </p>
                            </div>

                            <div class="flex justify-end">
                                <button class="px-4 py-2 rounded-md bg-teal-600 text-white text-xs font-semibold uppercase tracking-widest hover:bg-teal-700">
                                    Grabar Archivo
                                </button>
                            </div>

                        </form>

                        <div class="mt-6 border-t border-gray-200 pt-4">

                            <p class="text-sm font-semibold text-gray-700 mb-3">Documentos Complementarios</p>

                            @php
                                $docs = $profile->legal_documents ?? [];
                            @endphp

                            @if(empty($docs))

                                <p class="text-sm text-gray-500">Aún no hay documentos cargados.</p>

                            @else

                                <div class="overflow-x-auto">
                                <table class="min-w-full text-sm">
                                    <thead>
                                        <tr class="text-left border-b bg-gray-50">
                                            <th class="py-2 px-3">Usuario</th>
                                            <th class="py-2 px-3">Nombre Documento</th>
                                            <th class="py-2 px-3">Última Actualización</th>
                                            <th class="py-2 px-3"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($docs as $index => $doc)
                                            <tr class="border-b">
                                                <td class="py-2 px-3 text-gray-600">{{ $doc['uploaded_by'] ?? '—' }}</td>
                                                <td class="py-2 px-3">{{ $doc['title'] ?? $doc['name'] ?? 'Documento' }}</td>
                                                <td class="py-2 px-3 text-gray-600 whitespace-nowrap">
                                                    {{ \Carbon\Carbon::parse($doc['uploaded_at'])->format('d-m-Y H:i') }}
                                                </td>
                                                <td class="py-2 px-3 text-right whitespace-nowrap">
                                                    <a href="{{ route('quality.profile.documents.download', $index) }}"
                                                       class="text-indigo-600 hover:underline text-xs font-medium">
                                                        Ver / Descargar
                                                    </a>
                                                    <span class="text-gray-300 mx-1">|</span>
                                                    <form method="POST" action="{{ route('quality.profile.documents.delete', $index) }}"
                                                          class="inline" onsubmit="return confirm('¿Eliminar este documento?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="text-red-600 hover:underline text-xs font-medium">
                                                            Eliminar
                                                        </button>
                                                    </form>
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

                {{-- Mapa de Procesos --}}
                <div x-show="section === 'process-map'" x-cloak>

                    <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden p-6">

                        <h3 class="font-semibold text-gray-900 mb-4">Mapa de Procesos</h3>

                        <form method="POST" action="{{ route('quality.profile.process-map.upload') }}"
                              enctype="multipart/form-data" class="space-y-3">
                            @csrf

                            <div>
                                <label class="block text-xs text-gray-500">Título del documento</label>
                                <input type="text" name="title" value="{{ $profile->process_map_title }}" required
                                       class="mt-1 w-full text-sm rounded-md border-gray-300">
                            </div>

                            <div>
                                <label class="block text-xs text-gray-500">Subir imagen</label>
                                <input type="file" name="file" accept="image/png,image/jpeg" required class="mt-1 w-full text-sm">
                                <p class="text-xs text-gray-400 mt-1">Solo PNG o JPG. Ancho recomendado: 600px.</p>
                            </div>

                            <div class="flex justify-end">
                                <button class="px-3 py-1.5 rounded-md bg-teal-600 text-white text-xs font-semibold hover:bg-teal-700">
                                    Grabar Documento
                                </button>
                            </div>
                        </form>

                        @if($profile->process_map_path)
                            <div class="mt-4 border-t border-gray-100 pt-4">
                                <img src="{{ Storage::url($profile->process_map_path) }}" alt="Mapa de Procesos" class="max-w-full rounded-lg border">
                            </div>
                        @endif

                    </div>

                </div>

                {{-- Organigrama --}}
                <div x-show="section === 'org-chart'" x-cloak>

                    <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden p-6">

                        <h3 class="font-semibold text-gray-900 mb-4">Organigrama</h3>

                        <form method="POST" action="{{ route('quality.profile.org-chart.upload') }}"
                              enctype="multipart/form-data" class="space-y-3">
                            @csrf

                            <div>
                                <label class="block text-xs text-gray-500">Título del documento</label>
                                <input type="text" name="title" value="{{ $profile->org_chart_title }}" required
                                       class="mt-1 w-full text-sm rounded-md border-gray-300">
                            </div>

                            <div>
                                <label class="block text-xs text-gray-500">Subir imagen</label>
                                <input type="file" name="file" accept="image/png,image/jpeg" required class="mt-1 w-full text-sm">
                                <p class="text-xs text-gray-400 mt-1">Solo PNG o JPG. Ancho recomendado: 600px.</p>
                            </div>

                            <div class="flex justify-end">
                                <button class="px-3 py-1.5 rounded-md bg-teal-600 text-white text-xs font-semibold hover:bg-teal-700">
                                    Grabar Documento
                                </button>
                            </div>
                        </form>

                        @if($profile->org_chart_path)
                            <div class="mt-4 border-t border-gray-100 pt-4">
                                <img src="{{ Storage::url($profile->org_chart_path) }}" alt="Organigrama" class="max-w-full rounded-lg border">
                            </div>
                        @endif

                    </div>

                </div>

            </div>

        </div>
    </div>
</x-app-layout>
