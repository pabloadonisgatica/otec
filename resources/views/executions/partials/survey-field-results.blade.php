@foreach ($fields as $result)
    <div class="border border-gray-200 rounded-lg p-4 mb-3">
        <div class="flex items-start justify-between gap-4 mb-3">
            <p class="text-sm font-medium text-gray-900">
                {{ $result['field']->label }}
            </p>
            <span class="text-xs text-gray-400 whitespace-nowrap">
                {{ $result['answered_count'] }} respuesta{{ $result['answered_count'] === 1 ? '' : 's' }}
            </span>
        </div>

        @if (array_key_exists('counts', $result))

            @if ($result['average'] !== null)
                <div class="flex items-baseline gap-2 mb-4">
                    <span class="text-2xl font-semibold text-indigo-700">{{ $result['average'] }}</span>
                    <span class="text-xs text-gray-400">promedio</span>
                </div>
            @endif

            @php
                $maxCount = max($result['counts']) ?: 1;
            @endphp

            <div class="space-y-2">
                @foreach ($result['counts'] as $option => $count)
                    @php
                        $widthPercent = $result['answered_count'] > 0
                            ? round(($count / $result['answered_count']) * 100)
                            : 0;
                    @endphp
                    <div class="flex items-center gap-3">
                        <span class="text-xs text-gray-600 w-6 text-right shrink-0">{{ $option }}</span>
                        <div class="flex-1 bg-gray-100 rounded-full h-4 overflow-hidden">
                            <div class="bg-indigo-500 h-4 rounded-full transition-all"
                                 style="width: {{ max($widthPercent, $count > 0 ? 4 : 0) }}%"></div>
                        </div>
                        <span class="text-xs text-gray-500 w-16 shrink-0">
                            {{ $count }} ({{ $widthPercent }}%)
                        </span>
                    </div>
                @endforeach
            </div>

        @else

            @if ($result['texts']->isNotEmpty())
                <ul class="text-sm text-gray-700 space-y-1.5">
                    @foreach ($result['texts'] as $text)
                        <li class="border-l-2 border-gray-200 pl-3">{{ $text }}</li>
                    @endforeach
                </ul>
            @else
                <p class="text-xs text-gray-400">Sin respuestas de texto.</p>
            @endif

        @endif
    </div>
@endforeach
