<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $questionnaire->name }}</h2>

            <a href="{{ route('questionnaires.index') }}" class="underline text-sm text-gray-700 hover:text-gray-900">Volver</a>
        </div>
    </x-slot>

    <script>
        /**
         * Constructor de preguntas. Todo el estado vive aquí y se envía de una vez al guardar.
         */
        window.questionnaireBuilder = function (initial, choiceTypes, scaleRanges) {
            let uid = 0;

            const question = (data = {}) => ({
                uid: ++uid,
                id: data.id ?? null,
                type: data.type ?? 'short_text',
                label: data.label ?? '',
                help: data.help ?? '',
                showHelp: !! (data.help ?? ''),
                required: data.required === true || data.required === '1' || data.required === 1,
                options_text: data.options_text ?? '',
                min_label: data.min_label ?? '',
                max_label: data.max_label ?? '',
            });

            const section = (data = {}) => ({
                uid: ++uid,
                id: data.id ?? null,
                title: data.title ?? '',
                description: data.description ?? '',
                questions: Object.values(data.questions ?? {}).map(question),
            });

            return {
                sections: Object.values(initial ?? {}).map(section),
                dirty: false,

                init() {
                    if (this.sections.length === 0) {
                        this.sections.push(section({ questions: [{}] }));
                    }

                    window.addEventListener('beforeunload', (event) => {
                        if (this.dirty) {
                            event.preventDefault();
                            event.returnValue = '';
                        }
                    });
                },

                isChoice(q) { return choiceTypes.includes(q.type); },
                isScale(q) { return q.type in scaleRanges; },
                scaleValues(q) {
                    const [min, max] = scaleRanges[q.type];
                    return Array.from({ length: max - min + 1 }, (_, i) => min + i);
                },

                /** Número global de la pregunta (P1, P2…), contando todas las secciones. */
                number(si, qi) {
                    return this.sections.slice(0, si).reduce((total, s) => total + s.questions.length, 0) + qi + 1;
                },
                totalQuestions() {
                    return this.sections.reduce((total, s) => total + s.questions.length, 0);
                },

                addSection() {
                    this.sections.push(section({ questions: [{}] }));
                    this.dirty = true;
                },
                removeSection(si) {
                    const count = this.sections[si].questions.length;
                    if (count && ! confirm('¿Eliminar la sección y sus ' + count + ' pregunta(s)?')) return;
                    this.sections.splice(si, 1);
                    this.dirty = true;
                },
                moveSection(si, step) {
                    const to = si + step;
                    if (to < 0 || to >= this.sections.length) return;
                    this.sections.splice(to, 0, this.sections.splice(si, 1)[0]);
                    this.dirty = true;
                },

                addQuestion(si, type) {
                    if (! type) return;
                    this.sections[si].questions.push(question({ type }));
                    this.dirty = true;
                },
                duplicateQuestion(si, qi) {
                    const copy = question({ ...this.sections[si].questions[qi], id: null });
                    this.sections[si].questions.splice(qi + 1, 0, copy);
                    this.dirty = true;
                },
                removeQuestion(si, qi) {
                    this.sections[si].questions.splice(qi, 1);
                    this.dirty = true;
                },
                /** Sube o baja una pregunta; en el borde pasa a la sección vecina. */
                moveQuestion(si, qi, step) {
                    const list = this.sections[si].questions;
                    const to = qi + step;

                    if (to >= 0 && to < list.length) {
                        list.splice(to, 0, list.splice(qi, 1)[0]);
                    } else if (to < 0 && si > 0) {
                        this.sections[si - 1].questions.push(list.splice(qi, 1)[0]);
                    } else if (to >= list.length && si < this.sections.length - 1) {
                        this.sections[si + 1].questions.unshift(list.splice(qi, 1)[0]);
                    } else {
                        return;
                    }
                    this.dirty = true;
                },
                isFirst(si, qi) { return si === 0 && qi === 0; },
                isLast(si, qi) { return si === this.sections.length - 1 && qi === this.sections[si].questions.length - 1; },

                field(si, qi, name) {
                    return 'sections[' + si + '][questions][' + qi + '][' + name + ']';
                },
            };
        };
    </script>

    <div class="py-6 pb-28">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <x-breadcrumb :items="$breadcrumbs" />

            @include('questionnaires._tabs', ['current' => 'builder'])

            @if ($responsesCount > 0)
                <div class="mb-6 rounded-md border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                    <strong>Esta encuesta ya tiene {{ $responsesCount }} {{ $responsesCount === 1 ? 'respuesta' : 'respuestas' }}.</strong>
                    Corregir textos no afecta los resultados. Si eliminas una pregunta, sus respuestas se conservan con el texto original.
                    Evita cambiar el tipo o las opciones de preguntas ya respondidas: los resultados quedarían mezclados.
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 rounded-md border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                    <p class="font-medium">No se guardó. Revisa:</p>
                    <ul class="mt-1 list-disc list-inside">
                        @foreach (collect($errors->all())->unique() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('questionnaires.builder.update', $questionnaire) }}"
                  x-data="questionnaireBuilder(@js($sections), @js($choiceTypes), @js($scaleRanges))"
                  @input="dirty = true" @submit="dirty = false"
                  class="space-y-6">
                @csrf
                @method('PUT')

                <template x-for="(section, si) in sections" :key="section.uid">
                    <x-ui.card>
                        {{-- Encabezado de sección --}}
                        <div class="flex items-start gap-3 border-b border-gray-200 px-6 py-4">
                            <input type="hidden" :name="'sections[' + si + '][id]'" :value="section.id ?? ''">

                            <div class="flex-1 space-y-2">
                                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500"
                                   x-text="'Sección ' + (si + 1) + ' de ' + sections.length"></p>
                                <input type="text" x-model="section.title" :name="'sections[' + si + '][title]'" maxlength="255"
                                       class="w-full rounded-md border-gray-300 font-medium"
                                       placeholder="Título de la sección (opcional)">
                                <textarea x-model="section.description" :name="'sections[' + si + '][description]'" rows="1" maxlength="2000"
                                          class="w-full rounded-md border-gray-300 text-sm"
                                          placeholder="Descripción o instrucciones de la sección (opcional)"></textarea>
                            </div>

                            <div class="flex shrink-0 items-center gap-1 pt-6 text-sm">
                                <button type="button" @click="moveSection(si, -1)" :disabled="si === 0"
                                        class="rounded px-2 py-1 text-gray-500 hover:bg-gray-100 disabled:opacity-30" title="Subir sección">↑</button>
                                <button type="button" @click="moveSection(si, 1)" :disabled="si === sections.length - 1"
                                        class="rounded px-2 py-1 text-gray-500 hover:bg-gray-100 disabled:opacity-30" title="Bajar sección">↓</button>
                                <button type="button" @click="removeSection(si)" x-show="sections.length > 1"
                                        class="rounded px-2 py-1 text-red-600 hover:bg-red-50" title="Eliminar sección">Eliminar</button>
                            </div>
                        </div>

                        {{-- Preguntas --}}
                        <div class="divide-y divide-gray-100">
                            <template x-for="(q, qi) in section.questions" :key="q.uid">
                                <div class="px-6 py-5 space-y-3">
                                    <input type="hidden" :name="field(si, qi, 'id')" :value="q.id ?? ''">
                                    <input type="hidden" :name="field(si, qi, 'required')" :value="q.required ? 1 : 0">

                                    <div class="flex flex-wrap items-center gap-3">
                                        <span class="rounded bg-indigo-50 px-2 py-0.5 text-xs font-semibold text-indigo-700" x-text="'P' + number(si, qi)"></span>

                                        <select x-model="q.type" :name="field(si, qi, 'type')" class="rounded-md border-gray-300 text-sm">
                                            @foreach ($types as $key => $label)
                                                <option value="{{ $key }}">{{ $label }}</option>
                                            @endforeach
                                        </select>

                                        <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                                            <input type="checkbox" x-model="q.required" @change="dirty = true" class="rounded border-gray-300 text-indigo-600">
                                            Obligatoria
                                        </label>

                                        <div class="ms-auto flex items-center gap-1 text-sm">
                                            <button type="button" @click="moveQuestion(si, qi, -1)" :disabled="isFirst(si, qi)"
                                                    class="rounded px-2 py-1 text-gray-500 hover:bg-gray-100 disabled:opacity-30" title="Subir">↑</button>
                                            <button type="button" @click="moveQuestion(si, qi, 1)" :disabled="isLast(si, qi)"
                                                    class="rounded px-2 py-1 text-gray-500 hover:bg-gray-100 disabled:opacity-30" title="Bajar">↓</button>
                                            <button type="button" @click="duplicateQuestion(si, qi)"
                                                    class="rounded px-2 py-1 text-gray-600 hover:bg-gray-100">Duplicar</button>
                                            <button type="button" @click="removeQuestion(si, qi)"
                                                    class="rounded px-2 py-1 text-red-600 hover:bg-red-50">Eliminar</button>
                                        </div>
                                    </div>

                                    <input type="text" x-model="q.label" :name="field(si, qi, 'label')" maxlength="500" required
                                           class="w-full rounded-md border-gray-300" placeholder="Escribe la pregunta">

                                    <div>
                                        <button type="button" x-show="! q.showHelp" @click="q.showHelp = true"
                                                class="text-xs text-indigo-700 hover:underline">+ Agregar texto de ayuda</button>
                                        <input type="text" x-show="q.showHelp" x-cloak x-model="q.help" :name="field(si, qi, 'help')" maxlength="1000"
                                               class="w-full rounded-md border-gray-300 text-sm" placeholder="Texto de ayuda bajo la pregunta (opcional)">
                                    </div>

                                    {{-- Opciones --}}
                                    <div x-show="isChoice(q)" x-cloak>
                                        <label class="block text-xs font-medium text-gray-600">Opciones (una por línea)</label>
                                        <textarea x-model="q.options_text" :name="field(si, qi, 'options_text')" rows="4" :disabled="! isChoice(q)"
                                                  class="mt-1 w-full rounded-md border-gray-300 text-sm"
                                                  placeholder="Muy satisfecho&#10;Satisfecho&#10;Insatisfecho"></textarea>
                                    </div>

                                    {{-- Escalas --}}
                                    <div x-show="isScale(q)" x-cloak class="space-y-2">
                                        <div class="flex flex-wrap gap-1">
                                            <template x-for="value in (isScale(q) ? scaleValues(q) : [])" :key="value">
                                                <span class="flex h-8 w-8 items-center justify-center rounded border border-gray-300 text-xs text-gray-600" x-text="value"></span>
                                            </template>
                                        </div>
                                        <div class="grid grid-cols-2 gap-3">
                                            <input type="text" x-model="q.min_label" :name="field(si, qi, 'min_label')" :disabled="! isScale(q)" maxlength="100"
                                                   class="rounded-md border-gray-300 text-sm"
                                                   :placeholder="q.type === 'nps' ? 'Nada probable' : 'Texto del valor mínimo (opcional)'">
                                            <input type="text" x-model="q.max_label" :name="field(si, qi, 'max_label')" :disabled="! isScale(q)" maxlength="100"
                                                   class="rounded-md border-gray-300 text-sm text-right"
                                                   :placeholder="q.type === 'nps' ? 'Muy probable' : 'Texto del valor máximo (opcional)'">
                                        </div>
                                    </div>

                                    {{-- Vista previa simple de los tipos sin configuración --}}
                                    <p x-show="q.type === 'yes_no'" x-cloak class="text-sm text-gray-500">○ Sí &nbsp;&nbsp; ○ No</p>
                                    <p x-show="q.type === 'date'" x-cloak class="text-sm text-gray-500">Selector de fecha (dd-mm-aaaa)</p>
                                    <p x-show="q.type === 'short_text'" class="text-sm text-gray-400">Respuesta de una línea</p>
                                    <p x-show="q.type === 'long_text'" x-cloak class="text-sm text-gray-400">Respuesta de varias líneas</p>
                                </div>
                            </template>

                            <p x-show="section.questions.length === 0" class="px-6 py-5 text-sm text-gray-500">
                                Esta sección no tiene preguntas.
                            </p>
                        </div>

                        {{-- Agregar pregunta --}}
                        <div class="border-t border-gray-200 px-6 py-4">
                            <select @change="addQuestion(si, $event.target.value); $event.target.value = ''"
                                    class="rounded-md border-gray-300 text-sm text-indigo-700">
                                <option value="">+ Agregar pregunta…</option>
                                @foreach ($types as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </x-ui.card>
                </template>

                <button type="button" @click="addSection()"
                        class="w-full rounded-lg border-2 border-dashed border-gray-300 py-4 text-sm font-medium text-gray-600 hover:border-indigo-400 hover:text-indigo-700">
                    + Agregar sección
                </button>

                {{-- Barra de guardado --}}
                <div class="fixed inset-x-0 bottom-0 z-20 border-t border-gray-200 bg-white">
                    <div class="mx-auto flex max-w-4xl items-center justify-between gap-4 px-4 py-3 sm:px-6 lg:px-8">
                        <p class="text-sm text-gray-600">
                            <span x-text="totalQuestions() + (totalQuestions() === 1 ? ' pregunta' : ' preguntas')"></span>
                            · <span x-text="sections.length + (sections.length === 1 ? ' sección' : ' secciones')"></span>
                            <span x-show="dirty" x-cloak class="ms-2 font-medium text-amber-700">Cambios sin guardar</span>
                        </p>
                        <button type="submit" class="px-4 py-2 rounded bg-indigo-600 text-white hover:bg-indigo-500">
                            Guardar preguntas
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
