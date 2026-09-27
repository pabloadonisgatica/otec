<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Diploma {{ $diploma->code }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden p-6 space-y-6">

                <div>
                    <p class="text-sm text-gray-500">Participante</p>
                    <p class="font-medium text-gray-900">
                        {{ $diploma->snapshot['participant']['full_name'] ?? '-' }}
                        <span class="text-gray-500 font-normal">
                            ({{ $diploma->snapshot['participant']['rut'] ?? '-' }})
                        </span>
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Curso / Ejecución</p>
                    <p class="font-medium text-gray-900">
                        {{ $diploma->snapshot['course']['name'] ?? '-' }}
                    </p>
                    <p class="text-sm text-gray-500">
                        {{ $diploma->execution->internal_code ?? '-' }}
                        — {{ $diploma->snapshot['execution']['start_date'] ?? '' }}
                        a {{ $diploma->snapshot['execution']['end_date'] ?? '' }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Emitido</p>
                    <p class="font-medium text-gray-900">
                        {{ optional($diploma->issued_at)->format('d-m-Y H:i') }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-500 mb-2">Verificación</p>
                    <div class="flex items-start gap-4" x-data="{ copied: false }">
                        <img src="{{ $qrDataUri }}" alt="QR de verificación" class="h-32 w-32 border rounded-lg p-2">
                        <div class="text-sm space-y-2 min-w-0">
                            <a href="{{ $validationUrl }}" target="_blank"
                               class="block text-indigo-600 hover:underline break-all">{{ $validationUrl }}</a>
                            <button type="button"
                                    @click="navigator.clipboard.writeText('{{ $validationUrl }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                    class="px-3 py-1.5 rounded-lg border border-gray-300 text-xs text-gray-700 hover:bg-gray-50"
                                    x-text="copied ? 'Copiado' : 'Copiar link'"></button>
                            <p class="text-xs text-gray-400">
                                El QR del diploma lleva a este link. También se puede verificar ingresando el código en
                                <a href="{{ route('diplomas.verify') }}" target="_blank" class="underline">{{ route('diplomas.verify') }}</a>.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex gap-3 pt-4 border-t">
                    <a href="{{ route('diplomas.pdf', $diploma) }}" target="_blank"
                       class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md text-xs font-semibold uppercase tracking-widest hover:bg-indigo-500">
                        Descargar PDF
                    </a>

                    <a href="{{ route('diplomas.index') }}" class="text-sm text-gray-600 hover:underline self-center">
                        Volver
                    </a>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
