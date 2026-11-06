@use('App\Models\ProviderEvaluation', 'PE')

@php
    $input = 'mt-1 w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500';

    $initialScores = [];
    foreach (array_keys(PE::CRITERIA) as $key) {
        $initialScores[$key] = (int) old("scores.{$key}", $evaluation->scoreFor($key) ?? 0);
    }
@endphp

<div class="bg-white shadow-sm sm:rounded-lg overflow-hidden"
     x-data="{
        scores: @js($initialScores),
        max: {{ max(array_keys(PE::SCALE)) }},
        get filled() { return Object.values(this.scores).filter(v => v > 0) },
        get complete() { return this.filled.length === Object.keys(this.scores).length },
        get percent() {
            if (! this.complete) return null;
            const sum = this.filled.reduce((a, b) => a + Number(b), 0);
            return Math.round(sum / (this.filled.length * this.max) * 10000) / 100;
        },
        get result() {
            if (this.percent === null) return null;
            if (this.percent >= {{ PE::APPROVED_FROM }}) return 'approved';
            if (this.percent >= {{ PE::CONDITIONAL_FROM }}) return 'conditional';
            return 'rejected';
        },
        labels: @js(PE::RESULTS),
        badges: @js(PE::RESULT_BADGES),
     }">

    <div class="p-6 space-y-4 border-b border-gray-200">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium">Tipo</label>
                <select name="type" class="{{ $input }}">
                    @foreach(PE::TYPES as $value => $label)
                        <option value="{{ $value }}" @selected(old('type', $evaluation->type) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('type') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium">Fecha de evaluación</label>
                <input type="date" name="evaluated_at" required
                       value="{{ old('evaluated_at', optional($evaluation->evaluated_at)->format('Y-m-d')) }}" class="{{ $input }}">
                @error('evaluated_at') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium">Evaluador</label>
                <select name="evaluator_id" required class="{{ $input }}">
                    <option value="">Selecciona</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" @selected(old('evaluator_id', $evaluation->evaluator_id) == $user->id)>{{ $user->name }}</option>
                    @endforeach
                </select>
                @error('evaluator_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>

    {{-- Criterios --}}
    <div class="divide-y divide-gray-100">
        @foreach(PE::CRITERIA as $key => $criterion)
            <div class="px-6 py-4 grid grid-cols-1 md:grid-cols-5 gap-3 md:items-center">
                <div class="md:col-span-2">
                    <div class="text-sm font-medium text-gray-800">{{ $criterion['label'] }}</div>
                    <div class="text-xs text-gray-500">{{ $criterion['help'] }}</div>
                    @error("scores.{$key}") <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-3 flex flex-wrap gap-2">
                    @foreach(PE::SCALE as $value => $label)
                        <label class="cursor-pointer">
                            <input type="radio" name="scores[{{ $key }}]" value="{{ $value }}"
                                   x-model.number="scores.{{ $key }}" class="sr-only peer">
                            <span class="inline-flex flex-col items-center w-20 px-2 py-1.5 rounded-md border border-gray-300 text-xs text-gray-600
                                         peer-checked:border-indigo-600 peer-checked:bg-indigo-50 peer-checked:text-indigo-700 hover:bg-gray-50">
                                <span class="text-base font-semibold">{{ $value }}</span>
                                {{ $label }}
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    {{-- Resultado calculado --}}
    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex flex-wrap items-center gap-3 text-sm">
        <span class="text-gray-600">Resultado:</span>
        <template x-if="percent === null">
            <span class="text-gray-500">califica todos los criterios</span>
        </template>
        <template x-if="percent !== null">
            <span class="flex items-center gap-2">
                <span class="font-semibold text-gray-800" x-text="percent + '%'"></span>
                <span class="px-2 py-1 rounded-full text-xs font-medium" :class="badges[result]" x-text="labels[result]"></span>
            </span>
        </template>
        <span class="text-xs text-gray-500 md:ml-auto">
            Aprobado desde {{ PE::APPROVED_FROM }}% · Con observaciones desde {{ PE::CONDITIONAL_FROM }}%
        </span>
    </div>

    <div class="p-6 space-y-4 border-t border-gray-200">
        <div>
            <label class="block text-sm font-medium">Observaciones</label>
            <textarea name="observations" rows="3" class="{{ $input }}"
                      placeholder="Hechos que respaldan la evaluación, acuerdos con el proveedor, acciones a seguir...">{{ old('observations', $evaluation->observations) }}</textarea>
            @error('observations') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="md:w-1/3">
            <label class="block text-sm font-medium">Próxima reevaluación</label>
            <input type="date" name="next_evaluation_at"
                   value="{{ old('next_evaluation_at', optional($evaluation->next_evaluation_at)->format('Y-m-d')) }}" class="{{ $input }}">
            <p class="text-xs text-gray-500 mt-1">Por defecto, {{ PE::DEFAULT_REEVALUATION_MONTHS }} meses después.</p>
            @error('next_evaluation_at') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
    </div>
</div>
