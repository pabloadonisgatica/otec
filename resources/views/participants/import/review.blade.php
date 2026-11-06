<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Carga masiva de participantes
            </h2>

            <a href="{{ route('participants.import.columns', $token) }}"
               class="underline text-sm text-gray-700 hover:text-gray-900">
                Cambiar columnas
            </a>
        </div>
    </x-slot>

    @php
        $counts = $review['counts'];
        $notImported = $counts['error'] + $counts['repeated'] + $counts['existing'];
        $badges = [
            'new' => ['Nuevo', 'bg-green-100 text-green-800'],
            'existing' => ['Ya existe', 'bg-blue-50 text-blue-700'],
            'repeated' => ['Repetido', 'bg-amber-100 text-amber-800'],
            'error' => ['Con error', 'bg-red-100 text-red-800'],
        ];
    @endphp

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            <x-breadcrumb :items="$breadcrumbs" />

            @include('participants.import._steps', ['current' => 3])

            {{-- Resumen --}}
            <div class="mb-6 grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach (['new' => 'Se importarán', 'existing' => 'Ya existen', 'repeated' => 'Repetidos en el archivo', 'error' => 'Con errores'] as $status => $label)
                    <x-ui.card class="px-5 py-4">
                        <p class="text-xs text-gray-500">{{ $label }}</p>
                        <p class="mt-1 text-2xl font-semibold {{ $status === 'new' ? 'text-green-700' : ($status === 'error' && $counts[$status] ? 'text-red-700' : 'text-gray-900') }}">
                            {{ $counts[$status] }}
                        </p>
                    </x-ui.card>
                @endforeach
            </div>

            <x-ui.card>
                <div class="flex flex-wrap items-center justify-between gap-4 px-6 py-4 border-b border-gray-200">
                    <p class="text-sm text-gray-700">
                        {{ $state['file_name'] }} · Empresa: <span class="font-medium">{{ $company?->name }}</span>
                    </p>

                    @if ($notImported)
                        <a href="{{ route('participants.import.errors', $token) }}"
                           class="text-sm text-indigo-700 underline hover:text-indigo-900">
                            Descargar las {{ $notImported }} filas no importadas (CSV)
                        </a>
                    @endif
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-left text-xs uppercase tracking-wider text-gray-500">
                            <tr>
                                <th class="px-4 py-3">Fila</th>
                                <th class="px-4 py-3">RUT</th>
                                <th class="px-4 py-3">Nombres</th>
                                <th class="px-4 py-3">Apellidos</th>
                                <th class="px-4 py-3">Correo</th>
                                <th class="px-4 py-3">Teléfono</th>
                                <th class="px-4 py-3">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($review['rows'] as $row)
                                <tr class="align-top">
                                    <td class="px-4 py-3 text-gray-500">{{ $row['line'] }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap">{{ $row['data']['rut'] ?: '—' }}</td>
                                    <td class="px-4 py-3">{{ $row['data']['first_name'] ?: '—' }}</td>
                                    <td class="px-4 py-3">{{ $row['data']['last_name'] ?: '—' }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $row['data']['email'] ?? '—' }}</td>
                                    <td class="px-4 py-3 text-gray-600 whitespace-nowrap">{{ $row['data']['phone'] ?? '—' }}</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-block rounded-full px-2 py-0.5 text-xs font-medium {{ $badges[$row['status']][1] }}">
                                            {{ $badges[$row['status']][0] }}
                                        </span>
                                        @foreach ($row['errors'] as $message)
                                            <p class="mt-1 text-xs text-gray-600">{{ $message }}</p>
                                        @endforeach
                                        @foreach ($row['warnings'] as $message)
                                            <p class="mt-1 text-xs text-amber-700">{{ $message }}</p>
                                        @endforeach
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="flex flex-wrap items-center justify-end gap-3 px-6 py-4 border-t border-gray-200">
                    <a href="{{ route('participants.import.columns', $token) }}"
                       class="px-4 py-2 rounded bg-gray-100 text-gray-800 hover:bg-gray-200">
                        Volver a columnas
                    </a>

                    @if ($counts['new'] > 0)
                        <form method="POST" action="{{ route('participants.import.confirm', $token) }}"
                              x-data="{ busy: false }" @submit="busy = true">
                            @csrf
                            <button type="submit" :disabled="busy"
                                    class="px-4 py-2 rounded bg-indigo-600 text-white hover:bg-indigo-500 disabled:opacity-50"
                                    x-text="busy ? 'Importando…' : 'Importar {{ $counts['new'] }} {{ $counts['new'] === 1 ? 'participante' : 'participantes' }}'">
                                Importar {{ $counts['new'] }} {{ $counts['new'] === 1 ? 'participante' : 'participantes' }}
                            </button>
                        </form>
                    @else
                        <span class="text-sm text-gray-500">No hay participantes nuevos para importar.</span>
                    @endif
                </div>
            </x-ui.card>

        </div>
    </div>
</x-app-layout>
