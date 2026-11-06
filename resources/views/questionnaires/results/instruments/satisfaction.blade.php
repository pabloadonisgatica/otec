{{-- Resumen de la encuesta de satisfacción libre. Recibe $data (summary del instrumento). --}}
@php
    use App\Services\Questionnaires\Instruments\SatisfactionInstrument as SI;
    $num = fn ($value, $decimals = 1) => SI::number($value, $decimals);
@endphp

<x-ui.card class="mb-4">
    <dl class="px-6 py-5 grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
        <div>
            <dt class="text-xs text-gray-500">Relator evaluado</dt>
            <dd class="mt-1 text-base font-semibold text-gray-900">{{ $data['instructor'] }}</dd>
        </div>
        <div>
            <dt class="text-xs text-gray-500">Plantilla</dt>
            <dd class="mt-1 text-gray-900">{{ $data['template'] ?? '—' }}</dd>
        </div>
        <div>
            <dt class="text-xs text-gray-500">Promedio general</dt>
            <dd class="mt-1">
                @if ($data['average'] !== null)
                    <span class="text-2xl font-semibold text-gray-900">{{ $num($data['average']) }}</span>
                    @if ($data['scale_max'])
                        <span class="text-gray-500">de {{ $data['scale_max'] }}</span>
                    @endif
                @else
                    <span class="text-gray-500">Sin preguntas con nota numérica</span>
                @endif
            </dd>
        </div>
    </dl>
</x-ui.card>

<div class="space-y-6">
    @foreach ($data['sections'] as $section)
        <div>
            @if ($section['title'])
                <h3 class="mb-2 text-sm font-semibold uppercase tracking-wide text-indigo-700">{{ $section['title'] }}</h3>
            @endif

            <div class="space-y-4">
                @foreach ($section['items'] as $item)
                    <x-ui.card>
                        <div class="px-6 py-5">
                            <div class="flex flex-wrap items-start justify-between gap-2">
                                <h4 class="text-sm font-semibold text-gray-900">{{ $item['number'] }}. {{ $item['label'] }}</h4>
                                @if (($item['average'] ?? null) !== null)
                                    <span class="text-sm text-gray-700 whitespace-nowrap">
                                        Promedio <strong class="text-gray-900">{{ $num($item['average'], 2) }}</strong>
                                    </span>
                                @endif
                            </div>
                            <p class="mt-1 text-xs text-gray-500">
                                Respondida por {{ $item['answered'] }} de {{ $item['total'] }}
                                @if ($item['total'] > 0) ({{ $num($item['answered'] * 100 / $item['total'], 0) }}%) @endif
                            </p>

                            <div class="mt-4">
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
                                @elseif ($item['samples'])
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
                            </div>
                        </div>
                    </x-ui.card>
                @endforeach
            </div>
        </div>
    @endforeach
</div>
