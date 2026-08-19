<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Evaluación de Gerencia — {{ $response->instructor->name }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            @include('quality._nav')

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden p-6 space-y-4">

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Ejecución</p>
                        <p class="text-gray-900">
                            {{ $response->execution->course_name ?? optional($response->execution->course)->name }}
                            <span class="text-xs text-gray-400 block">{{ $response->execution->internal_code }}</span>
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Firma</p>
                        <p class="text-gray-900">{{ $response->evaluator_name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Fecha</p>
                        <p class="text-gray-900">{{ $response->submitted_at->format('d-m-Y H:i') }}</p>
                    </div>
                </div>

                <div class="border-t border-gray-100 pt-4 space-y-3">
                    @foreach($response->answers as $answer)
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-700">{{ $answer->question->text }}</span>
                            <span class="font-medium text-gray-900">{{ $answer->score ?? 'N/A' }}</span>
                        </div>
                    @endforeach
                </div>

                @if($response->suggestions)
                    <div class="border-t border-gray-100 pt-4">
                        <p class="text-sm text-gray-500">Comentarios</p>
                        <p class="text-gray-900 whitespace-pre-line">{{ $response->suggestions }}</p>
                    </div>
                @endif

            </div>

            <a href="{{ route('quality.surveys.index') }}" class="text-sm text-gray-600 hover:underline mt-6 inline-block">
                ← Volver al listado
            </a>

        </div>
    </div>
</x-app-layout>
