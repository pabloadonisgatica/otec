{{-- Resumen de Habilidades Gerenciales y Liderazgo. Recibe $data (summary del instrumento). --}}
@php
    $num = fn ($value, $decimals = 1) => number_format((float) $value, $decimals, ',', '.');
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <x-ui.card>
        <div class="px-6 py-5">
            <h3 class="text-sm font-semibold text-gray-900">Estilos de liderazgo (promedio del grupo)</h3>
            <div class="mt-4 space-y-2">
                @foreach ($data['styles'] as $style)
                    <div class="grid grid-cols-12 items-center gap-3 text-sm">
                        <span class="col-span-3 text-gray-700">{{ $style['label'] }}</span>
                        <div class="col-span-7 h-3 rounded-full bg-gray-100 overflow-hidden">
                            <div class="h-full rounded-full" style="width: {{ $style['average'] ?? 0 }}%; background: {{ $style['color'] }}"></div>
                        </div>
                        <span class="col-span-2 text-right text-gray-700">{{ $style['average'] !== null ? $num($style['average']) . '%' : '—' }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </x-ui.card>

    <x-ui.card>
        <div class="px-6 py-5">
            <h3 class="text-sm font-semibold text-gray-900">Flexibilidad para liderar</h3>
            @if ($data['average_points'] !== null)
                <p class="mt-1 text-xs text-gray-500">
                    Puntaje promedio {{ $num($data['average_points']) }} (de −24 a +24) · {{ $data['average_flexibility'] }}
                </p>
            @endif
            <div class="mt-4 space-y-2">
                @foreach ($data['flexibility'] as $row)
                    <div class="grid grid-cols-12 items-center gap-3 text-sm">
                        <span class="col-span-5 text-gray-700">{{ $row['label'] }}</span>
                        <div class="col-span-4 h-3 rounded-full bg-gray-100 overflow-hidden">
                            <div class="h-full rounded-full bg-indigo-500" style="width: {{ $row['percent'] }}%"></div>
                        </div>
                        <span class="col-span-3 text-right text-gray-600 whitespace-nowrap">{{ $num($row['percent']) }}% · {{ $row['count'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </x-ui.card>
</div>

<x-ui.card class="mt-4">
    <div class="px-6 py-5">
        <h3 class="text-sm font-semibold text-gray-900">Estilo elegido en cada situación</h3>
        <div class="mt-2 flex flex-wrap gap-4 text-xs text-gray-600">
            @foreach ($data['styles'] as $style)
                <span class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full" style="background: {{ $style['color'] }}"></span>{{ $style['label'] }}</span>
            @endforeach
        </div>

        <div class="mt-4 space-y-3">
            @foreach ($data['questions'] as $question)
                <div>
                    <p class="text-sm text-gray-800">
                        <span class="font-medium">{{ $question['number'] }}.</span>
                        <span class="text-gray-600">{{ \Illuminate\Support\Str::limit($question['situation'], 110) }}</span>
                    </p>
                    <div class="mt-1 flex h-3 overflow-hidden rounded-full bg-gray-100">
                        @foreach ($question['styles'] as $style)
                            @if ($style['percent'] > 0)
                                <div style="width: {{ $style['percent'] }}%; background: {{ $style['color'] }}"
                                     title="{{ $style['label'] }}: {{ $style['count'] }} ({{ $num($style['percent']) }}%)"></div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-ui.card>
