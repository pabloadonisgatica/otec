{{-- Estilos de liderazgo (% que suman 100) y flexibilidad. Recibe $result. --}}
@php
    use App\Services\Questionnaires\Instruments\LeadershipInstrument as LI;
    $styles = $result['styles'] ?? [];

    // Gráfico de torta con CSS (conic-gradient), sin librerías.
    $start = 0;
    $slices = [];
    foreach (LI::STYLES as $key => $style) {
        $percent = $styles[$key]['percent'] ?? 0;
        if ($percent > 0) {
            $slices[] = "{$style['color']} {$start}% " . ($start + $percent) . '%';
            $start += $percent;
        }
    }
@endphp

<div class="flex flex-col sm:flex-row items-center gap-6">
    <div class="h-40 w-40 shrink-0 rounded-full ring-4 ring-white shadow-sm"
         style="background: conic-gradient({{ implode(', ', $slices) ?: '#e5e7eb 0 100%' }});"
         role="img" aria-label="Distribución de estilos de liderazgo"></div>

    <ul class="w-full divide-y divide-gray-100 rounded-lg border border-gray-200">
        @foreach (LI::STYLES as $key => $style)
            <li class="flex items-center justify-between px-4 py-2.5 text-sm">
                <span class="flex items-center gap-2 text-gray-800">
                    <span class="h-3 w-3 rounded-full" style="background: {{ $style['color'] }}"></span>
                    {{ $style['label'] }}
                </span>
                <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold text-white" style="background: {{ $style['color'] }}">
                    {{ $styles[$key]['percent'] ?? 0 }}%
                </span>
            </li>
        @endforeach
    </ul>
</div>

<div class="mt-5 flex flex-wrap items-center justify-between gap-2 rounded-lg bg-indigo-50 px-4 py-3">
    <span class="text-sm text-gray-800">Tu flexibilidad para liderar es:</span>
    <span class="rounded-full bg-indigo-600 px-3 py-1 text-sm font-semibold text-white">{{ $result['flexibility'] ?? '—' }}</span>
</div>
