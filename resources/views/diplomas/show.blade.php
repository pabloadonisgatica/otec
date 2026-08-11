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

                @if($diploma->qr_path)
                    <div>
                        <p class="text-sm text-gray-500 mb-2">Código QR de validación</p>
                        <img src="{{ Storage::url($diploma->qr_path) }}" alt="QR" class="h-32 w-32 border rounded-lg p-2">
                    </div>
                @endif

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
