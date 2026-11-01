{{-- Resumen de la Escala de Evaluación de Grupo. Recibe $data (summary del instrumento). --}}
@php
    use App\Services\Questionnaires\Instruments\GroupEvaluationInstrument as GE;
    $num = fn ($value, $decimals = 1) => number_format((float) $value, $decimals, ',', '.');
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    @foreach ($data['dimensions'] as $key => $dimension)
        <x-ui.card>
            <div class="px-6 py-5">
                <h3 class="text-sm font-semibold text-gray-900">{{ $dimension['label'] }}</h3>
                <p class="mt-1 text-xs text-gray-500">{{ $dimension['detail'] }}</p>

                @if ($dimension['average'] !== null)
                    <p class="mt-4">
                        <span class="text-3xl font-semibold text-gray-900">{{ $num($dimension['average']) }}</span>
                        <span class="text-sm text-gray-500">promedio de {{ GE::SCORE_MAX }}</span>
                        <span class="ms-1 text-sm text-gray-700">· nivel {{ $dimension['average_level'] }}</span>
                    </p>
                @endif

                <div class="mt-4 space-y-2">
                    @foreach ($dimension['distribution'] as $row)
                        <div class="grid grid-cols-12 items-center gap-3 text-sm">
                            <span class="col-span-3 text-gray-700">{{ $row['label'] }}</span>
                            <div class="col-span-6 h-3 rounded-full bg-gray-100 overflow-hidden">
                                <div class="h-full rounded-full" style="width: {{ $row['percent'] }}%; background: {{ $row['color'] }}"></div>
                            </div>
                            <span class="col-span-3 text-right text-gray-600 whitespace-nowrap">{{ $num($row['percent']) }}% · {{ $row['count'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </x-ui.card>
    @endforeach
</div>

<x-ui.card class="mt-4">
    <div class="px-6 py-5">
        <h3 class="text-sm font-semibold text-gray-900">Promedio por ítem</h3>
        <p class="mt-1 text-xs text-gray-500">Escala 1 a 8. Ítems 1 a 5: Madurez para la Tarea · Ítems 6 a 10: Madurez Psicológica.</p>

        <div class="mt-4 space-y-2">
            @foreach ($data['items'] as $item)
                <div class="grid grid-cols-12 items-center gap-3 text-sm {{ $item['number'] === 6 ? 'pt-3 border-t border-gray-100' : '' }}">
                    <span class="col-span-12 sm:col-span-5 text-gray-700">{{ $item['number'] }}. {{ $item['title'] }}</span>
                    <div class="col-span-10 sm:col-span-6 h-3 rounded-full bg-gray-100 overflow-hidden">
                        <div class="h-full rounded-full {{ $item['dimension'] === 'mt' ? 'bg-indigo-500' : 'bg-teal-500' }}"
                             style="width: {{ $item['average'] !== null ? $item['average'] * 100 / GE::MAX : 0 }}%"></div>
                    </div>
                    <span class="col-span-2 sm:col-span-1 text-right text-gray-700">{{ $item['average'] !== null ? $num($item['average']) : '—' }}</span>
                </div>
            @endforeach
        </div>
    </div>
</x-ui.card>
