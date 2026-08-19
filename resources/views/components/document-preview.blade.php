@props(['version'])

@if($version)

    @php
        $isExternal = $version->isExternalLink();
        $ext = $isExternal ? null : strtolower(pathinfo($version->file_name ?? '', PATHINFO_EXTENSION));
        $isPdf = $ext === 'pdf';
        $isImage = in_array($ext, ['png', 'jpg', 'jpeg', 'gif', 'webp']);
    @endphp

    <div class="border border-gray-200 rounded-lg p-4">

        <div class="flex items-center justify-between gap-4 mb-3">
            <div>
                <p class="font-medium text-gray-900">
                    Versión {{ $version->version_number }}
                    — {{ $isExternal ? 'Enlace externo' : $version->file_name }}
                </p>
                <p class="text-xs text-gray-500">
                    Subido por {{ $version->uploaded_by }} el {{ $version->uploaded_at->format('d-m-Y H:i') }}
                </p>
            </div>

            <a href="{{ route('quality.documents.versions.download', $version) }}" target="_blank"
               class="px-3 py-1.5 rounded-md bg-teal-600 text-white text-xs font-semibold hover:bg-teal-700 whitespace-nowrap shrink-0">
                {{ $isExternal ? 'Abrir enlace' : 'Descargar' }}
            </a>
        </div>

        @if($isPdf)

            <iframe src="{{ route('quality.documents.versions.view', $version) }}"
                    class="w-full border rounded" style="height: 32rem;"></iframe>

        @elseif($isImage)

            <img src="{{ route('quality.documents.versions.view', $version) }}"
                 alt="{{ $version->file_name }}" class="max-w-full rounded border">

        @elseif(!$isExternal)

            <p class="text-sm text-gray-400">
                📄 Vista previa no disponible para este tipo de archivo — usa "Descargar" para verlo.
            </p>

        @endif

    </div>

@else

    <p class="text-sm text-gray-500">Aún no se ha subido ningún documento.</p>

@endif
