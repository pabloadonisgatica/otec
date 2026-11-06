{{-- Datos generales de una encuesta (crear y editar). --}}
@php
    $exists = $questionnaire->exists;
    $fixedFields = collect($types)->mapWithKeys(fn ($t) => [$t->value => $t->fixedRespondentFields()]);
    $storedFields = (array) ($questionnaire->respondent_fields ?? []);
    $closesAt = old('closes_at', $questionnaire->closes_at?->copy()->timezone(\App\Models\Questionnaire::TIMEZONE)->format('Y-m-d\TH:i'));
    $modes = ['' => 'No pedir', 'optional' => 'Opcional', 'required' => 'Obligatorio'];
@endphp

<form method="POST" enctype="multipart/form-data"
      action="{{ $exists ? route('questionnaires.update', $questionnaire) : route('questionnaires.store') }}"
      x-data="{
          type: @js(old('type', $questionnaire->type?->value)),
          association: @js(old('association', $questionnaire->associationType())),
          fixed: @js($fixedFields),
      }"
      class="space-y-6">
    @csrf
    @if ($exists)
        @method('PUT')
    @endif

    @if ($errors->any())
        <div class="rounded-md border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Tipo --}}
    <x-ui.card>
        <div class="p-6">
            <h3 class="text-sm font-semibold text-gray-900">Tipo de encuesta</h3>

            @if ($exists)
                <p class="mt-2 text-sm text-gray-800">{{ $questionnaire->type->label() }}</p>
                <p class="text-xs text-gray-500">{{ $questionnaire->type->description() }} El tipo no se puede cambiar después de crear la encuesta.</p>
            @else
                <p class="mt-1 text-xs text-gray-500">No se puede cambiar después.</p>

                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach ($types as $option)
                        <label class="cursor-pointer rounded-lg border p-4 transition"
                               :class="type === '{{ $option->value }}' ? 'border-indigo-600 bg-indigo-50' : 'border-gray-200 hover:bg-gray-50'">
                            <input type="radio" name="type" value="{{ $option->value }}" x-model="type" class="sr-only">
                            <span class="block text-sm font-medium text-gray-900">{{ $option->label() }}</span>
                            <span class="mt-1 block text-xs text-gray-500">{{ $option->description() }}</span>
                        </label>
                    @endforeach
                </div>
            @endif
        </div>
    </x-ui.card>

    {{-- Encuesta de satisfacción: plantilla y relator --}}
    <div x-show="type === 'satisfaction'" x-cloak>
        <x-ui.card>
            <div class="p-6 space-y-4">
                <div>
                    <h3 class="text-sm font-semibold text-gray-900">Encuesta de satisfacción</h3>
                    <p class="mt-1 text-xs text-gray-500">Mismas preguntas y mismo diseño que la encuesta de satisfacción de las ejecuciones. Las preguntas no se pueden editar.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Plantilla *</label>
                    @if ($exists)
                        <p class="mt-1 text-sm text-gray-800">{{ $questionnaire->settings['template']['name'] ?? '—' }}</p>
                        <p class="text-xs text-gray-500">Se copió al crear la encuesta. Si la plantilla cambia en Configuración, esta encuesta no cambia.</p>
                    @else
                        <select name="survey_template_id" :disabled="type !== 'satisfaction'" class="mt-1 w-full rounded-md border-gray-300">
                            <option value="">Selecciona una plantilla…</option>
                            @foreach ($surveyTemplates as $template)
                                <option value="{{ $template->id }}" @selected(old('survey_template_id') == $template->id)>{{ $template->name }}</option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-gray-500">Las plantillas activas de Configuración → Plantillas de satisfacción.</p>
                    @endif
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Nombre del relator *</label>
                    <input type="text" name="instructor_name" maxlength="150"
                           value="{{ old('instructor_name', $questionnaire->settings['instructor_name'] ?? '') }}"
                           :disabled="type !== 'satisfaction' || {{ $hasResponses ? 'true' : 'false' }}"
                           class="mt-1 w-full rounded-md border-gray-300 disabled:bg-gray-50 disabled:text-gray-500"
                           placeholder="Ej: María González">
                    <p class="mt-1 text-xs text-gray-500">
                        {{ $hasResponses
                            ? 'No se puede cambiar porque la encuesta ya tiene respuestas.'
                            : 'Aparece en la encuesta como «Evaluando a …». Un relator por encuesta.' }}
                    </p>
                </div>
            </div>
        </x-ui.card>
    </div>

    {{-- Datos generales --}}
    <x-ui.card>
        <div class="p-6 space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Nombre *</label>
                <input type="text" name="name" value="{{ old('name', $questionnaire->name) }}" required maxlength="255"
                       class="mt-1 w-full rounded-md border-gray-300" placeholder="Ej: Satisfacción curso Liderazgo PACCAR 2026">
                <p class="mt-1 text-xs text-gray-500">Se muestra como título a quien responde.</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Descripción o instrucciones</label>
                <textarea name="description" rows="3" maxlength="2000"
                          class="mt-1 w-full rounded-md border-gray-300">{{ old('description', $questionnaire->description) }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Fecha de cierre</label>
                    <input type="datetime-local" name="closes_at" value="{{ $closesAt }}" class="mt-1 w-full rounded-md border-gray-300">
                    <p class="mt-1 text-xs text-gray-500">Hora de Chile. Vacío = sin cierre automático.</p>
                </div>

                <div class="flex items-start pt-6">
                    <input type="hidden" name="is_active" value="0">
                    <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                        <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-indigo-600"
                               @checked(old('is_active', $questionnaire->is_active))>
                        Activa (recibe respuestas)
                    </label>
                </div>
            </div>
        </div>
    </x-ui.card>

    {{-- Asociación --}}
    <x-ui.card>
        <div class="p-6 space-y-4">
            <div>
                <h3 class="text-sm font-semibold text-gray-900">Asociar a</h3>
                <p class="mt-1 text-xs text-gray-500">Opcional. Sirve para ordenar y filtrar resultados; no envía la encuesta a nadie.</p>
            </div>

            <div class="flex flex-wrap gap-4 text-sm">
                @foreach (['none' => 'Nada', 'company' => 'Empresa', 'course' => 'Curso', 'execution' => 'Ejecución'] as $key => $label)
                    <label class="inline-flex items-center gap-2 text-gray-700">
                        <input type="radio" name="association" value="{{ $key }}" x-model="association" class="border-gray-300 text-indigo-600">
                        {{ $label }}
                    </label>
                @endforeach
            </div>

            <select name="company_id" x-show="association === 'company'" x-cloak :disabled="association !== 'company'" class="w-full rounded-md border-gray-300">
                <option value="">Selecciona una empresa…</option>
                @foreach ($companies as $company)
                    <option value="{{ $company->id }}" @selected(old('company_id', $questionnaire->company_id) == $company->id)>{{ $company->name }}</option>
                @endforeach
            </select>

            <select name="course_id" x-show="association === 'course'" x-cloak :disabled="association !== 'course'" class="w-full rounded-md border-gray-300">
                <option value="">Selecciona un curso…</option>
                @foreach ($courses as $course)
                    <option value="{{ $course->id }}" @selected(old('course_id', $questionnaire->course_id) == $course->id)>{{ $course->name }}</option>
                @endforeach
            </select>

            <select name="execution_id" x-show="association === 'execution'" x-cloak :disabled="association !== 'execution'" class="w-full rounded-md border-gray-300">
                <option value="">Selecciona una ejecución…</option>
                @foreach ($executions as $execution)
                    <option value="{{ $execution->id }}" @selected(old('execution_id', $questionnaire->execution_id) == $execution->id)>
                        {{ $execution->internal_code }} · {{ $execution->course_name }}
                    </option>
                @endforeach
            </select>
        </div>
    </x-ui.card>

    {{-- Datos de quien responde --}}
    <x-ui.card>
        <div class="p-6 space-y-4">
            <div>
                <h3 class="text-sm font-semibold text-gray-900">Datos de quien responde</h3>
                <p class="mt-1 text-xs text-gray-500" x-show="! fixed[type]">
                    Si no pides ningún dato, la encuesta es anónima.
                </p>
                <p class="mt-1 text-xs text-gray-500" x-show="fixed[type] && Object.keys(fixed[type]).length" x-cloak>
                    Este instrumento pide estos datos siempre, porque envía el resultado por correo.
                </p>
                <p class="mt-1 text-xs text-gray-500" x-show="fixed[type] && ! Object.keys(fixed[type]).length" x-cloak>
                    Esta encuesta es anónima, igual que la encuesta de satisfacción de las ejecuciones.
                </p>
                <p class="mt-1 text-xs text-gray-500" x-show="association !== 'none'" x-cloak>
                    Como la encuesta está asociada, la organización no se pregunta: se completa con la empresa y se muestra como dato de contexto.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach ($respondentFields as $key => $label)
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ $label }}</label>

                        {{-- Libre: configurable --}}
                        <select name="respondent_fields[{{ $key }}]" x-show="! fixed[type]" :disabled="!! fixed[type]"
                                class="mt-1 w-full rounded-md border-gray-300">
                            @foreach ($modes as $mode => $modeLabel)
                                <option value="{{ $mode }}" @selected(old("respondent_fields.$key", $storedFields[$key] ?? '') === $mode)>{{ $modeLabel }}</option>
                            @endforeach
                        </select>

                        {{-- Instrumento: fijo --}}
                        <p x-show="fixed[type]" x-cloak class="mt-1 rounded-md bg-gray-50 px-3 py-2 text-sm text-gray-700"
                           x-text="fixed[type] && fixed[type]['{{ $key }}'] ? 'Obligatorio' : 'No se pide'"></p>
                    </div>
                @endforeach
            </div>
        </div>
    </x-ui.card>

    {{-- Banner --}}
    <x-ui.card>
        <div class="p-6 space-y-3">
            <h3 class="text-sm font-semibold text-gray-900">Banner</h3>
            <p class="text-xs text-gray-500">Imagen opcional en la parte superior de la encuesta (recomendado 1200 × 300 px, máximo 2 MB).</p>

            @if ($questionnaire->bannerUrl())
                <img src="{{ $questionnaire->bannerUrl() }}" alt="" class="max-h-32 rounded border border-gray-200">
                <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                    <input type="hidden" name="remove_banner" value="0">
                    <input type="checkbox" name="remove_banner" value="1" class="rounded border-gray-300 text-red-600">
                    Quitar banner
                </label>
            @endif

            <input type="file" name="banner" accept="image/*" class="block w-full text-sm">
        </div>
    </x-ui.card>

    <div class="flex justify-end gap-2">
        <a href="{{ route('questionnaires.index') }}" class="px-4 py-2 rounded bg-gray-100 text-gray-800 hover:bg-gray-200">Cancelar</a>
        <button type="submit" class="px-4 py-2 rounded bg-indigo-600 text-white hover:bg-indigo-500">
            {{ $exists ? 'Guardar cambios' : 'Crear encuesta' }}
        </button>
    </div>
</form>
