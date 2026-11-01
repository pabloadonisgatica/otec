<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $questionnaire->name }}</h2>
            <a href="{{ route('questionnaires.index') }}" class="underline text-sm text-gray-700 hover:text-gray-900">Volver</a>
        </div>
    </x-slot>

    @php
        $num = fn ($value, $decimals = 1) => number_format((float) $value, $decimals, ',', '.');
        $toneBar = ['good' => 'bg-green-500', 'neutral' => 'bg-gray-400', 'bad' => 'bg-red-500'];
    @endphp

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <x-breadcrumb :items="$breadcrumbs" />

            @include('questionnaires._tabs', ['current' => 'results'])
            @include('questionnaires.results._header')

            <x-ui.card class="mb-6">
                <div class="px-6 py-5 flex flex-wrap items-baseline gap-x-6 gap-y-1">
                    <p class="text-3xl font-semibold text-gray-900">{{ $summary['total'] }}</p>
                    <p class="text-sm text-gray-600">
                        {{ $summary['total'] === 1 ? 'respuesta' : 'respuestas' }}
                        {{ $filters ? 'en el período filtrado' : 'en total' }}
                    </p>
                </div>
            </x-ui.card>

            @if (! $questionnaire->hasBuilder() && ! isset($summary['instrument']))
                <x-ui.card>
                    <p class="p-6 text-sm text-gray-600">
                        Los resultados de este instrumento ({{ $questionnaire->type->label() }}) se habilitan junto con su formulario.
                    </p>
                </x-ui.card>
            @elseif ($summary['total'] === 0)
                <x-ui.card>
                    <p class="p-6 text-sm text-gray-600">
                        Aún no hay respuestas{{ $filters ? ' en este período' : '' }}.
                        <a href="{{ route('questionnaires.share', $questionnaire) }}" class="text-indigo-700 underline">Compartir la encuesta</a>
                    </p>
                </x-ui.card>
            @elseif (isset($summary['instrument']))
                @include('questionnaires.results.instruments.' . $questionnaire->type->value, ['data' => $summary['instrument']])
            @else
                <div class="space-y-4">
                    @foreach ($summary['questions'] as $item)
                        <x-ui.card>
                            <div class="px-6 py-5">
                                <div class="flex flex-wrap items-start justify-between gap-2">
                                    <h3 class="text-sm font-semibold text-gray-900">{{ $item['title'] }}</h3>
                                    <span class="text-xs text-gray-500 whitespace-nowrap">
                                        @if ($item['deleted'])
                                            <span class="rounded bg-gray-100 px-2 py-0.5 text-gray-600">Pregunta eliminada</span>
                                        @endif
                                        {{ $item['type_label'] }}
                                    </span>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">
                                    Respondida por {{ $item['answered'] }} de {{ $item['total'] }}
                                    @if ($item['total'] > 0) ({{ $num($item['answered'] * 100 / $item['total'], 0) }}%) @endif
                                </p>

                                <div class="mt-4">
                                    {{-- Opciones --}}
                                    @if ($item['kind'] === 'choice')
                                        <div class="space-y-2">
                                            @foreach ($item['rows'] as $row)
                                                <div class="grid grid-cols-12 items-center gap-3 text-sm">
                                                    <span class="col-span-12 sm:col-span-4 text-gray-700">{{ $row['label'] }}</span>
                                                    <div class="col-span-9 sm:col-span-6 h-3 rounded-full bg-gray-100 overflow-hidden">
                                                        <div class="h-full rounded-full bg-indigo-500" style="width: {{ $row['percent'] }}%"></div>
                                                    </div>
                                                    <span class="col-span-3 sm:col-span-2 text-right text-gray-600 whitespace-nowrap">{{ $num($row['percent']) }}% · {{ $row['count'] }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                        @if ($item['multiple'] ?? false)
                                            <p class="mt-3 text-xs text-gray-500">Se podía elegir más de una opción: los porcentajes pueden sumar más de 100%.</p>
                                        @endif

                                    {{-- Escalas y NPS --}}
                                    @elseif (in_array($item['kind'], ['scale', 'nps'], true))
                                        <div class="flex flex-wrap items-baseline gap-x-8 gap-y-2">
                                            @if ($item['kind'] === 'nps' && $item['nps'] !== null)
                                                <div>
                                                    <p class="text-3xl font-semibold {{ $item['nps'] > 0 ? 'text-green-700' : ($item['nps'] < 0 ? 'text-red-700' : 'text-gray-900') }}">
                                                        {{ $item['nps'] > 0 ? '+' : '' }}{{ $item['nps'] }}
                                                    </p>
                                                    <p class="text-xs text-gray-500">NPS (de −100 a +100)</p>
                                                </div>
                                            @endif
                                            @if ($item['average'] !== null)
                                                <div>
                                                    <p class="text-3xl font-semibold text-gray-900">{{ $num($item['average'], 2) }}</p>
                                                    <p class="text-xs text-gray-500">Promedio (escala {{ $item['min'] }} a {{ $item['max'] }})</p>
                                                </div>
                                            @endif
                                        </div>

                                        @if ($item['kind'] === 'nps')
                                            <div class="mt-4 space-y-2">
                                                @foreach ($item['groups'] as $group)
                                                    <div class="grid grid-cols-12 items-center gap-3 text-sm">
                                                        <span class="col-span-12 sm:col-span-4 text-gray-700">{{ $group['label'] }}</span>
                                                        <div class="col-span-9 sm:col-span-6 h-3 rounded-full bg-gray-100 overflow-hidden">
                                                            <div class="h-full rounded-full {{ $toneBar[$group['tone']] }}" style="width: {{ $group['percent'] }}%"></div>
                                                        </div>
                                                        <span class="col-span-3 sm:col-span-2 text-right text-gray-600 whitespace-nowrap">{{ $num($group['percent']) }}% · {{ $group['count'] }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif

                                        {{-- Distribución por valor --}}
                                        @php $peak = max(1, max(array_column($item['rows'], 'count'))); @endphp
                                        <div class="mt-5 flex items-end gap-1 h-28">
                                            @foreach ($item['rows'] as $row)
                                                <div class="flex flex-1 flex-col items-center justify-end h-full" title="{{ $row['label'] }}: {{ $row['count'] }} ({{ $num($row['percent']) }}%)">
                                                    <span class="text-[10px] text-gray-500">{{ $row['count'] ?: '' }}</span>
                                                    <div class="w-full rounded-t bg-indigo-500" style="height: {{ $row['count'] * 100 / $peak }}%; min-height: {{ $row['count'] ? '2px' : '0' }}"></div>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="flex gap-1 border-t border-gray-200 pt-1">
                                            @foreach ($item['rows'] as $row)
                                                <span class="flex-1 text-center text-xs text-gray-600">{{ $row['label'] }}</span>
                                            @endforeach
                                        </div>
                                        @if ($item['min_label'] || $item['max_label'])
                                            <div class="mt-1 flex justify-between text-xs text-gray-500">
                                                <span>{{ $item['min_label'] }}</span>
                                                <span>{{ $item['max_label'] }}</span>
                                            </div>
                                        @endif

                                    {{-- Texto y fecha --}}
                                    @else
                                        @if ($item['samples'])
                                            <ul class="divide-y divide-gray-100 rounded-md border border-gray-200">
                                                @foreach ($item['samples'] as $sample)
                                                    <li class="px-4 py-2 text-sm">
                                                        <p class="text-gray-800 whitespace-pre-line">{{ $sample['value'] }}</p>
                                                        <p class="text-xs text-gray-400">{{ $sample['date'] }}</p>
                                                    </li>
                                                @endforeach
                                            </ul>
                                            @if ($item['answered'] > count($item['samples']))
                                                <p class="mt-2 text-xs text-gray-500">
                                                    Se muestran las {{ count($item['samples']) }} más recientes de {{ $item['answered'] }}. Exporta el CSV para verlas todas.
                                                </p>
                                            @endif
                                        @else
                                            <p class="text-sm text-gray-500">Sin respuestas.</p>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </x-ui.card>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
