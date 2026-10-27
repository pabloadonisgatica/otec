{{-- Pestañas de una encuesta. Uso: @include('questionnaires._tabs', ['current' => 'general']) --}}
@php
    $tabs = ['general' => ['Datos generales', route('questionnaires.edit', $questionnaire)]];

    if ($questionnaire->hasBuilder()) {
        $tabs['builder'] = ['Preguntas (' . $questionnaire->questions()->count() . ')', route('questionnaires.builder.edit', $questionnaire)];
    }
@endphp

<div class="mb-6 flex flex-wrap items-end justify-between gap-3 border-b border-gray-200">
    <nav class="-mb-px flex gap-6 text-sm">
        @foreach ($tabs as $key => [$label, $url])
            <a href="{{ $url }}"
               class="border-b-2 px-1 pb-3 font-medium {{ $current === $key ? 'border-indigo-600 text-indigo-700' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                {{ $label }}
            </a>
        @endforeach
    </nav>

    <form method="POST" action="{{ route('questionnaires.duplicate', $questionnaire) }}" class="pb-2"
          onsubmit="return confirm('¿Crear una copia de esta encuesta con sus preguntas? La copia tiene un link nuevo y no trae respuestas.')">
        @csrf
        <button type="submit" class="text-sm text-gray-600 hover:text-gray-900 underline">Duplicar encuesta</button>
    </form>
</div>
