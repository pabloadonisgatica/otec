<x-ui.section class="mt-6">

    <x-slot:header>
        <x-ui.section-header
            title="Encuesta de satisfacción"
            subtitle="Se envía por correo a cada participante con un link personalizado." />
    </x-slot:header>

    <div class="grid grid-cols-3 gap-4 mb-6">

        <x-ui.stat-card
            title="Participantes"
            :value="$surveySummary['total_participants']" />

        <x-ui.stat-card
            title="Invitados"
            :value="$surveySummary['invited_count']" />

        <x-ui.stat-card
            title="Respondidas"
            :value="$surveySummary['submitted_count']" />

    </div>

    <form
        method="POST"
        action="{{ route('executions.survey.send', $execution) }}"
        onsubmit="return confirm('¿Enviar la encuesta por correo a los participantes que aún no la han recibido?')">

        @csrf

        <button
            type="submit"
            class="px-5 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">

            📧 Enviar encuesta a participantes

        </button>

        <p class="text-xs text-gray-400 mt-2">
            Solo se envía a quienes tengan correo registrado y no hayan sido invitados aún.
        </p>

    </form>

    @if($surveySummary['submitted_count'] > 0)

        <div class="mt-8 border-t pt-6">

            <h3 class="font-semibold text-gray-900 mb-4">
                Resultados por pregunta
            </h3>

            <div class="overflow-x-auto border border-gray-200 rounded-xl">

                <table class="min-w-full text-sm">

                    <thead>
                        <tr class="bg-gray-50">
                            <th class="text-left p-3">Área</th>
                            <th class="text-left p-3">Pregunta</th>
                            <th class="p-3 text-center" style="width: 100px;">Promedio</th>
                            <th class="p-3 text-center" style="width: 100px;">Respuestas</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($surveySummary['question_averages'] as $row)
                            <tr class="border-t border-gray-100">
                                <td class="p-3 text-gray-500">{{ $row['question']->area }}</td>
                                <td class="p-3">{{ $row['question']->text }}</td>
                                <td class="p-3 text-center font-semibold">
                                    {{ $row['average'] ?? '—' }}
                                </td>
                                <td class="p-3 text-center text-gray-500">
                                    {{ $row['answers_count'] }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>

            </div>

        </div>

        @if($surveySummary['suggestions']->isNotEmpty())

            <div class="mt-8 border-t pt-6">

                <h3 class="font-semibold text-gray-900 mb-4">
                    Sugerencias y reclamos
                </h3>

                <div class="space-y-3">
                    @foreach($surveySummary['suggestions'] as $suggestion)
                        <div class="border border-gray-200 rounded-lg p-3 text-sm text-gray-700">
                            {{ $suggestion }}
                        </div>
                    @endforeach
                </div>

            </div>

        @endif

    @else

        <div class="mt-8 border-t pt-6">
            <x-ui.empty-state
                title="Aún no hay respuestas"
                message="Cuando los participantes respondan la encuesta, los resultados aparecerán aquí." />
        </div>

    @endif

</x-ui.section>
