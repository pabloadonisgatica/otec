@props([
    'name' => 'documents',
    'label' => 'Documentos (PDF)',
    'existing' => [],
    'accept' => 'application/pdf',
    'type' => null,
    'ownerId' => null,

    // ✅ nuevo: qué mostrar
    'mode' => 'both', // 'upload' | 'manage' | 'both'
])

<div class="space-y-2">
    <label class="block text-sm font-medium">{{ $label }}</label>

@if($mode === 'upload' || $mode === 'both')

    <div>
        <label class="block text-sm font-medium">Nombre / Tipo de documento</label>
        <input type="text"
               name="document_label"
               value="{{ old('document_label', 'Documento') }}"
               class="mt-1 w-full rounded border-gray-300"
               placeholder="Ej: CV, Título, Certificación" />
        @error('document_label') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
    </div>

    <input type="file"
           name="{{ $name }}[]"
           accept="{{ $accept }}"
           multiple
           class="block w-full text-sm" />

    <p class="text-xs text-gray-500">Si subes varios PDFs a la vez, se guardan con el mismo nombre.</p>
@endif


    @if(($mode === 'manage' || $mode === 'both') && $type && $ownerId)
        @php
            $docs = is_array($existing) ? $existing : [];
        @endphp

        @if(count($docs))
            <div class="mt-3 space-y-2">
                <div class="text-sm font-medium">Archivos actuales</div>

                <ul class="space-y-2">
                    @foreach($docs as $i => $doc)
                        @php
                            $hasPath = is_array($doc) && !empty($doc['path'] ?? null);
                        @endphp
                        @continue(!$hasPath)

                        <li class="flex items-center justify-between gap-2">
                            <div class="text-sm">
                                <span class="font-medium">{{ $doc['label'] ?? 'Documento' }}:</span>
                                <span>{{ $doc['name'] ?? basename($doc['path']) }}</span>
                            </div>

                            <div class="flex gap-2">
                                <a class="text-blue-600 text-sm underline"
                                   href="{{ route('documents.show', [$type, $ownerId, $i]) }}"
                                   target="_blank">Ver</a>

                                <a class="text-blue-600 text-sm underline"
                                   href="{{ route('documents.download', [$type, $ownerId, $i]) }}">Descargar</a>

                                <form method="POST"
                                      action="{{ route('documents.delete', [$type, $ownerId, $i]) }}"
                                      onsubmit="return confirm('¿Eliminar este documento?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 text-sm underline">Eliminar</button>
                                </form>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    @endif
</div>
