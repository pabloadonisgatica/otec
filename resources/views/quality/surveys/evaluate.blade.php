<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Evaluación de Gerencia — {{ $instructor->name }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            @include('quality._nav')

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden p-6">

                <p class="text-sm text-gray-500 mb-6">
                    Ejecución: <span class="font-medium text-gray-900">{{ $execution->course_name ?? optional($execution->course)->name }}</span>
                    ({{ $execution->internal_code }})
                </p>

                <form method="POST" action="{{ route('quality.surveys.evaluate.store', [$execution, $instructor]) }}" class="space-y-8">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium">Firma (nombre de quien evalúa)</label>
                        <input type="text" name="evaluator_name" value="{{ old('evaluator_name') }}" required
                               placeholder="Ej: Gerente General - Manuel Bustamante"
                               class="mt-1 w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        @error('evaluator_name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    @foreach($questions as $question)
                        <div x-data="{ value: '{{ old('scores.' . $question->id, '') }}' }">

                            <p class="text-sm font-medium text-gray-900 mb-3">{{ $question->text }}</p>

                            <div class="grid grid-cols-4 sm:grid-cols-8 gap-2">
                                @foreach([1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 'N/A' => ''] as $label => $value)
                                    <label class="flex flex-col items-center gap-1 border rounded-lg py-2 text-xs cursor-pointer transition"
                                           :class="value === '{{ $value }}' ? 'border-indigo-500 bg-indigo-50 text-indigo-700' : 'border-gray-300 hover:bg-gray-50'">
                                        <input type="radio" name="scores[{{ $question->id }}]" value="{{ $value }}"
                                               x-model="value" class="h-4 w-4">
                                        {{ $label }}
                                    </label>
                                @endforeach
                            </div>

                            @error('scores.' . $question->id) <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    @endforeach

                    <div>
                        <label class="block text-sm font-medium">Comentarios / Sugerencias (opcional)</label>
                        <textarea name="suggestions" rows="3" class="mt-1 w-full rounded-md border-gray-300 text-sm">{{ old('suggestions') }}</textarea>
                    </div>

                    <div class="flex items-center gap-3 pt-2">
                        <button class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md text-xs font-semibold uppercase tracking-widest hover:bg-indigo-500">
                            Registrar Evaluación
                        </button>
                        <a href="{{ route('quality.surveys.index') }}" class="text-sm text-gray-600 hover:underline">Volver</a>
                    </div>

                </form>

            </div>
        </div>
    </div>
</x-app-layout>
