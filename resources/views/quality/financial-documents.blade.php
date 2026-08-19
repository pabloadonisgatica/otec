<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Requisitos Financieros</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <x-breadcrumb :items="[
                ['label' => 'Ver Norma', 'url' => route('quality.norm.index')],
                ['label' => 'Requisitos Financieros'],
            ]" />

            @if (session('status'))
                <div class="mb-4 p-3 rounded bg-green-50 text-green-700">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden p-6">

                <form method="POST" action="{{ route('quality.financial-documents.upload') }}"
                      enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium">Título</label>
                        <input type="text" name="title" value="{{ old('title') }}" required
                               placeholder="Ej: Presupuesto 2026, Balance 2025"
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
                        $docs = $profile->financial_documents ?? [];
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
                                            <a href="{{ route('quality.financial-documents.download', $index) }}"
                                               class="text-indigo-600 hover:underline text-xs font-medium">
                                                Ver / Descargar
                                            </a>
                                            <span class="text-gray-300 mx-1">|</span>
                                            <form method="POST" action="{{ route('quality.financial-documents.delete', $index) }}"
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
    </div>
</x-app-layout>
