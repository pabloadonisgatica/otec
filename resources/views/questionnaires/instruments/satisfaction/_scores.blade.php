{{-- Resultado de una respuesta de la encuesta de satisfacción libre. Recibe $result. --}}
@php use App\Services\Questionnaires\Instruments\SatisfactionInstrument as SI; @endphp

<dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
    <div>
        <dt class="text-xs text-gray-500">Relator evaluado</dt>
        <dd class="text-gray-900">{{ $result['instructor'] ?? '—' }}</dd>
    </div>
    <div>
        <dt class="text-xs text-gray-500">Promedio de las preguntas con nota</dt>
        <dd class="text-gray-900">
            @if (($result['average'] ?? null) !== null)
                <span class="text-2xl font-semibold">{{ SI::number($result['average']) }}</span>
                @if ($result['scale_max'] ?? null)
                    <span class="text-gray-500">de {{ $result['scale_max'] }}</span>
                @endif
            @else
                <span class="text-gray-500">Esta plantilla no tiene preguntas con nota numérica.</span>
            @endif
        </dd>
    </div>
</dl>
