{{-- Puntajes MT y MP con barra de bandas (Bajo 5-15, Moderado 16-30, Alto 31-40). Recibe $result. --}}
@php
    use App\Services\Questionnaires\Instruments\GroupEvaluationInstrument as GE;
    $dimensions = $result['dimensions'] ?? [];
@endphp

<div class="space-y-6">
    @foreach (GE::DIMENSIONS as $key => $dimension)
        @php $d = $dimensions[$key] ?? null; @endphp
        @if ($d)
            <div>
                <div class="flex flex-wrap items-baseline justify-between gap-2">
                    <p class="font-semibold text-gray-900">{{ $dimension['label'] }}</p>
                    <p>
                        <span class="text-2xl font-semibold text-gray-900">{{ $d['score'] }}</span>
                        <span class="text-sm text-gray-500">de {{ GE::SCORE_MAX }}</span>
                        <span class="ms-1 rounded-full px-2.5 py-0.5 text-xs font-semibold text-white" style="background: {{ GE::LEVELS[$d['level']]['color'] }}">
                            {{ $d['level_label'] }}
                        </span>
                    </p>
                </div>

                <div class="relative mt-2" role="img" aria-label="{{ $dimension['label'] }}: {{ $d['score'] }} de {{ GE::SCORE_MAX }}, nivel {{ $d['level_label'] }}">
                    <div class="flex h-3 overflow-hidden rounded-full">
                        <span style="flex: 11; background: {{ GE::LEVELS['low']['color'] }}"></span>
                        <span style="flex: 15; background: #ffc107"></span>
                        <span style="flex: 10; background: {{ GE::LEVELS['high']['color'] }}"></span>
                    </div>
                    <span class="absolute -top-1 -bottom-1 w-1 -ml-0.5 rounded bg-gray-900 ring-2 ring-white"
                          style="left: {{ GE::markerPosition($d['score']) }}%"></span>
                </div>
                <div class="mt-1 flex justify-between text-xs text-gray-500">
                    <span>Bajo</span><span>Moderado</span><span>Alto</span>
                </div>
                <p class="mt-1 text-xs text-gray-500">{{ $dimension['detail'] }}</p>
            </div>
        @endif
    @endforeach
</div>
