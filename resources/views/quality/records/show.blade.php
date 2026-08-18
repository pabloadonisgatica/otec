<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $record->name }}</h2>

            <a href="{{ route('quality.records.edit', $record) }}"
               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-md text-xs font-semibold uppercase tracking-widest hover:bg-gray-50">
                Editar
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <x-breadcrumb :items="[
                ['label' => 'Ver Norma', 'url' => route('quality.norm.index')],
                ['label' => 'Control de Registros', 'url' => route('quality.records.index')],
                ['label' => $record->name],
            ]" />

            @if (session('status'))
                <div class="mb-4 p-3 rounded bg-green-50 text-green-700">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden p-6 space-y-4">

                <div>
                    <p class="text-sm text-gray-500">Versión</p>
                    <p class="text-gray-900">v{{ $record->version }}</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Usuario Aprobación</p>
                        <p class="text-gray-900">{{ $record->approval_user ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Fecha Aprobación</p>
                        <p class="text-gray-900">{{ optional($record->approval_date)->format('d-m-Y') ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Protección</p>
                        <p class="text-gray-900">{{ $record->protection ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Almacenamiento</p>
                        <p class="text-gray-900">{{ $record->storage_location ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Tiempo Retención</p>
                        <p class="text-gray-900">{{ $record->retention_time ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Recuperación</p>
                        <p class="text-gray-900">{{ $record->recovery ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Disposición Final</p>
                        <p class="text-gray-900">{{ $record->final_disposition ?? '—' }}</p>
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
