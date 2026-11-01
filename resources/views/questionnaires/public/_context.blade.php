{{-- Empresa · Curso · Ejecución a los que está asociada la encuesta, como chips de contexto. --}}
@php $chips = $questionnaire->contextChips(); @endphp

@if ($chips)
    <div class="mt-3 flex flex-wrap gap-2">
        @foreach ($chips as $chip)
            <span class="inline-flex items-center gap-1.5 rounded-full border border-indigo-100 bg-indigo-50 px-3 py-1 text-xs text-indigo-800">
                <span class="font-medium text-indigo-500">{{ $chip['label'] }}</span>
                {{ $chip['value'] }}
            </span>
        @endforeach
    </div>
@endif
