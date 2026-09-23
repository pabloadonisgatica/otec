<x-ui.section class="mt-6">

    <x-slot:header>
        <x-ui.section-header
            title="Encuesta de satisfacción"
            subtitle="Se genera un link y QR únicos para esta ejecución, a partir de una plantilla." />
    </x-slot:header>

    @if ($execution->executionSurvey)

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">

            <div>
                <p class="text-sm text-gray-500 mb-1">Plantilla usada</p>
                <p class="font-medium text-gray-900 mb-4">
                    {{ $execution->executionSurvey->template->name }}
                </p>

                <p class="text-sm text-gray-500 mb-1">Link para compartir</p>
                <div class="flex items-center gap-2 mb-4" x-data="{ copied: false }">
                    <input
                        id="survey-link-{{ $execution->id }}"
                        type="text"
                        readonly
                        value="{{ $execution->executionSurvey->publicUrl() }}"
                        class="flex-1 text-sm rounded-lg border-gray-300 bg-gray-50"
                        onclick="this.select()">

                    {{-- Copiar --}}
                    <button
                        type="button"
                        @click="
                            navigator.clipboard.writeText('{{ $execution->executionSurvey->publicUrl() }}');
                            copied = true;
                            setTimeout(() => copied = false, 2000)
                        "
                        class="shrink-0 px-3 py-2 rounded-lg border border-gray-300 text-xs text-gray-700 hover:bg-gray-50 flex items-center gap-1.5 transition"
                        :class="copied ? 'border-green-400 text-green-700 bg-green-50' : ''">
                        <svg x-show="!copied" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                        <svg x-show="copied" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span x-text="copied ? 'Copiado' : 'Copiar'"></span>
                    </button>

                    {{-- Abrir encuesta --}}
                    <a href="{{ $execution->executionSurvey->publicUrl() }}"
                       target="_blank"
                       class="shrink-0 px-3 py-2 rounded-lg border border-indigo-300 text-xs text-indigo-700 bg-indigo-50 hover:bg-indigo-100 flex items-center gap-1.5 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                        Ver encuesta
                    </a>
                </div>

                <div class="flex flex-wrap gap-3">
                    {{-- Descargar QR --}}
                    <a href="{{ route('executions.survey.qr-download', $execution) }}"
                       class="px-3 py-1.5 rounded-lg border border-gray-300 text-xs text-gray-700 hover:bg-gray-50">
                        ⬇ Descargar QR
                    </a>

                    {{-- Borrar respuestas de prueba --}}
                    <form method="POST"
                          action="{{ route('executions.survey.clear-responses', $execution) }}"
                          onsubmit="return confirm('¿Eliminar TODAS las respuestas registradas? Esta acción no se puede deshacer.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="px-3 py-1.5 rounded-lg border border-orange-300 text-xs text-orange-600 hover:bg-orange-50">
                            🗑 Borrar respuestas de prueba
                        </button>
                    </form>

                    {{-- Eliminar encuesta completa --}}
                    <form method="POST"
                          action="{{ route('executions.survey-template.destroy', $execution) }}"
                          onsubmit="return confirm('¿Eliminar esta encuesta y sus respuestas? Esta acción no se puede deshacer.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 text-xs hover:underline">
                            Eliminar encuesta y elegir otra plantilla
                        </button>
                    </form>
                </div>
            </div>

            <div class="flex flex-col items-center">
                <div class="border border-gray-200 rounded-xl p-4 bg-white">
                    {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(180)->generate($execution->executionSurvey->publicUrl()) !!}
                </div>
                <p class="text-xs text-gray-400 mt-2">Código QR para entregar a los participantes</p>
            </div>

        </div>

        @if ($executionSurveyResults)

            <div class="mt-8 border-t pt-6">

                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-gray-900">
                        Resultados — {{ $executionSurveyResults['total_responses'] }} respuesta(s)
                    </h3>

                    @if ($executionSurveyResults['total_responses'] > 0)
                        <a href="{{ route('executions.survey.export', $execution) }}"
                           class="px-4 py-2 rounded-lg border border-gray-300 text-sm text-gray-700 hover:bg-gray-50">
                            ⬇ Descargar Excel
                        </a>
                    @endif
                </div>

                @if ($executionSurveyResults['total_responses'] === 0)

                    <x-ui.empty-state
                        title="Aún no hay respuestas"
                        message="Cuando los participantes respondan la encuesta, los resultados aparecerán aquí." />

                @else

                    @foreach ($executionSurveyResults['by_instructor'] as $row)
                        <div class="mb-6">
                            <h4 class="text-sm font-semibold text-gray-700 uppercase mb-3">
                                Relator: {{ $row['instructor']->name }}
                            </h4>
                            @foreach ($row['sections'] as $sectionResult)
                                @include('executions.partials.survey-field-results', ['fields' => $sectionResult['fields']])
                            @endforeach
                        </div>
                    @endforeach

                @endif

            </div>

        @endif

    @else

        <form method="POST" action="{{ route('executions.survey-template.store', $execution) }}" class="flex items-end gap-3">
            @csrf

            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Plantilla de encuesta</label>
                <select name="survey_template_id" class="w-full rounded-lg border-gray-300" required>
                    <option value="">Seleccione una plantilla...</option>
                    @foreach ($activeSurveyTemplates as $template)
                        <option value="{{ $template->id }}">{{ $template->name }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="px-5 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">
                Nueva encuesta
            </button>
        </form>

        @if ($activeSurveyTemplates->isEmpty())
            <p class="text-xs text-amber-600 mt-3">
                No hay plantillas activas todavía. Créalas en Académico → Templates Encuestas.
            </p>
        @endif

    @endif

</x-ui.section>
