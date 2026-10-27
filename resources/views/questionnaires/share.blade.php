<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $questionnaire->name }}</h2>
            <a href="{{ route('questionnaires.index') }}" class="underline text-sm text-gray-700 hover:text-gray-900">Volver</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <x-breadcrumb :items="$breadcrumbs" />

            @include('questionnaires._tabs', ['current' => 'share'])

            @if ($reason = $questionnaire->unavailableReason())
                <div class="mb-6 rounded-md border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                    <strong>Esta encuesta no recibe respuestas.</strong> {{ $reason }}
                    Quien abra el link verá ese mensaje.
                    <a href="{{ route('questionnaires.edit', $questionnaire) }}" class="underline">Cambiar en Datos generales</a>.
                </div>
            @elseif ($questionnaire->hasBuilder() && ! $questionnaire->questions()->exists())
                <div class="mb-6 rounded-md border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                    <strong>La encuesta aún no tiene preguntas.</strong>
                    <a href="{{ route('questionnaires.builder.edit', $questionnaire) }}" class="underline">Agrégalas</a> antes de compartir el link.
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Link --}}
                <x-ui.card class="md:col-span-2">
                    <div class="p-6 space-y-5">
                        <div x-data="{ copied: false }">
                            <label class="block text-sm font-medium text-gray-700">Link de la encuesta</label>
                            <div class="mt-1 flex gap-2">
                                <input type="text" readonly value="{{ $url }}" x-ref="link" @focus="$event.target.select()"
                                       class="flex-1 rounded-md border-gray-300 bg-gray-50 text-sm">
                                <button type="button"
                                        @click="navigator.clipboard.writeText($refs.link.value).then(() => { copied = true; setTimeout(() => copied = false, 2000) })"
                                        class="px-4 py-2 rounded bg-indigo-600 text-white text-sm hover:bg-indigo-500"
                                        x-text="copied ? '¡Copiado!' : 'Copiar'">Copiar</button>
                            </div>
                            <a href="{{ $url }}" target="_blank" class="mt-2 inline-block text-sm text-indigo-700 underline">Abrir la encuesta</a>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-gray-700">Enviar el link</p>
                            <p class="text-xs text-gray-500">Se abre tu WhatsApp o tu programa de correo con el mensaje listo; tú eliges a quién enviarlo.</p>
                            <div class="mt-2 flex flex-wrap gap-2">
                                <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener"
                                   class="px-4 py-2 rounded border border-gray-300 text-sm text-gray-700 hover:bg-gray-50">WhatsApp</a>
                                <a href="{{ $mailtoUrl }}"
                                   class="px-4 py-2 rounded border border-gray-300 text-sm text-gray-700 hover:bg-gray-50">Correo</a>
                            </div>
                        </div>

                        <div class="border-t border-gray-200 pt-4 text-sm text-gray-700">
                            <strong>{{ $responsesCount }}</strong> {{ $responsesCount === 1 ? 'respuesta' : 'respuestas' }}
                            @if ($lastResponseAt)
                                · última el {{ $lastResponseAt->copy()->timezone(\App\Models\Questionnaire::TIMEZONE)->format('d-m-Y H:i') }}
                            @endif
                            · Estado: {{ $questionnaire->statusLabel() }}
                            @if ($questionnaire->closes_at)
                                · {{ $questionnaire->status() === 'closed' ? 'Cerró' : 'Cierra' }} el {{ $questionnaire->closesAtLocal() }}
                            @endif
                        </div>
                    </div>
                </x-ui.card>

                {{-- QR --}}
                <x-ui.card>
                    <div class="p-6 text-center space-y-4">
                        <div class="mx-auto w-48 rounded bg-white p-2 [&>svg]:h-auto [&>svg]:w-full">{!! $qr !!}</div>
                        <div class="flex flex-col gap-2">
                            <a href="{{ route('questionnaires.qr.print', $questionnaire) }}" target="_blank"
                               class="px-4 py-2 rounded bg-indigo-600 text-white text-sm hover:bg-indigo-500">Imprimir QR</a>
                            <a href="{{ route('questionnaires.qr.download', $questionnaire) }}"
                               class="px-4 py-2 rounded border border-gray-300 text-sm text-gray-700 hover:bg-gray-50">Descargar QR (SVG)</a>
                        </div>
                        <p class="text-xs text-gray-500">Proyéctalo en la sala o imprímelo para que respondan desde el celular.</p>
                    </div>
                </x-ui.card>
            </div>
        </div>
    </div>
</x-app-layout>
