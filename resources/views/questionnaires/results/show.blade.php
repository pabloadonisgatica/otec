<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Respuesta N° {{ $response->id }}</h2>
            <a href="{{ route('questionnaires.results.responses', $questionnaire) }}" class="underline text-sm text-gray-700 hover:text-gray-900">Volver a las respuestas</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <x-breadcrumb :items="$breadcrumbs" />

            <x-ui.card>
                <dl class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-xs text-gray-500">Encuesta</dt>
                        <dd class="text-gray-900">{{ $questionnaire->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">Fecha</dt>
                        <dd class="text-gray-900">{{ $response->submittedAtLocal() }}</dd>
                    </div>
                    @foreach ($respondentFields as $field => $label)
                        <div>
                            <dt class="text-xs text-gray-500">{{ $label }}</dt>
                            <dd class="text-gray-900">{{ $response->{$field} ?: '—' }}</dd>
                        </div>
                    @endforeach
                </dl>
            </x-ui.card>

            @if ($instrument && $response->result)
                <x-ui.card>
                    <div class="px-6 py-5">
                        <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
                            <h3 class="text-sm font-semibold text-gray-900">{{ $instrument->title() }}</h3>
                            @if ($instrument->emailsResult())
                            <div class="flex items-center gap-3 text-sm">
                                <span class="{{ $response->result_emailed_at ? 'text-gray-500' : 'text-amber-700' }}">
                                    {{ $response->result_emailed_at
                                        ? 'Correo enviado el ' . $response->result_emailed_at->copy()->timezone(\App\Models\Questionnaire::TIMEZONE)->format('d-m-Y H:i')
                                        : 'El correo no se envió' }}
                                </span>
                                @if ($response->email)
                                    <form method="POST" action="{{ route('questionnaires.results.resend', [$questionnaire, $response]) }}">
                                        @csrf
                                        <button type="submit" class="text-indigo-700 underline">
                                            {{ $response->result_emailed_at ? 'Reenviar' : 'Enviar ahora' }}
                                        </button>
                                    </form>
                                @endif
                            </div>
                            @endif
                        </div>

                        @include('questionnaires.instruments.' . $instrument->key() . '._scores', ['result' => $response->result])
                    </div>
                </x-ui.card>
            @endif

            <x-ui.card>
                <dl class="divide-y divide-gray-100">
                    @forelse ($answers as $answer)
                        <div class="px-6 py-4">
                            <dt class="text-sm font-medium text-gray-900">
                                {{ $answer->question_label }}
                                @unless ($answer->questionnaire_question_id || $instrument)
                                    <x-ui.badge tone="attention" class="ms-1">Pregunta eliminada</x-ui.badge>
                                @endunless
                            </dt>
                            <dd class="mt-1 text-sm text-gray-700 whitespace-pre-line">
                                @if ($answer->question_type === 'date' && $answer->value)
                                    {{ \Illuminate\Support\Carbon::parse($answer->value)->format('d-m-Y') }}
                                @else
                                    {{ $answer->displayValue() }}
                                @endif
                            </dd>
                        </div>
                    @empty
                        <p class="px-6 py-5 text-sm text-gray-500">Esta respuesta no tiene preguntas respondidas.</p>
                    @endforelse
                </dl>
            </x-ui.card>

            <form method="POST" action="{{ route('questionnaires.results.destroy', [$questionnaire, $response]) }}"
                  onsubmit="return confirm('¿Eliminar esta respuesta? No se puede deshacer.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-sm text-red-600 hover:underline">Eliminar esta respuesta</button>
            </form>
        </div>
    </div>
</x-app-layout>
