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
                <div class="flex items-center gap-2 mb-4">
                    <input
                        type="text"
                        readonly
                        value="{{ $execution->executionSurvey->publicUrl() }}"
                        class="w-full text-sm rounded-lg border-gray-300 bg-gray-50"
                        onclick="this.select()">
                </div>

                <form
                    method="POST"
                    action="{{ route('executions.survey-template.destroy', $execution) }}"
                    onsubmit="return confirm('¿Eliminar esta encuesta y sus respuestas? Esta acción no se puede deshacer.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 text-sm hover:underline">
                        Eliminar encuesta y elegir otra plantilla
                    </button>
                </form>
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

                    @foreach ($executionSurveyResults['general_results'] as $sectionResult)
                        <div class="mb-6">
                            <h4 class="text-sm font-semibold text-gray-700 uppercase mb-3">
                                {{ $sectionResult['section']->title }}
                            </h4>
                            @include('executions.partials.survey-field-results', ['fields' => $sectionResult['fields']])
                        </div>
                    @endforeach

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
