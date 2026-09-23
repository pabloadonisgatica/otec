@php
    $initialSections = $template->exists
        ? $template->sections->map(function ($section) {
            return [
                'id' => $section->id,
                'title' => $section->title,
                'description' => $section->description,
                'fields' => $section->fields->map(function ($field) {
                    return [
                        'id' => $field->id,
                        'label' => $field->label,
                        'type' => $field->type,
                        'options_text' => is_array($field->options) ? implode(', ', $field->options) : '',
                        'required' => (bool) $field->required,
                    ];
                })->values()->all(),
            ];
        })->values()->all()
        : [
            [
                'id' => null,
                'title' => '',
                'description' => '',
                'fields' => [
                    ['id' => null, 'label' => '', 'type' => 'input', 'options_text' => '', 'required' => false],
                ],
            ],
        ];
@endphp

<form method="POST"
      enctype="multipart/form-data"
      action="{{ $template->exists ? route('survey-templates.update', $template) : route('survey-templates.store') }}"
      x-data="{
          sections: {{ json_encode($initialSections) }},
          fieldTypes: {{ json_encode(\App\Models\SurveyTemplateField::TYPES) }},
          addSection() {
              this.sections.push({ id: null, title: '', description: '', fields: [
                  { id: null, label: '', type: 'input', options_text: '', required: false }
              ] });
          },
          removeSection(i) {
              this.sections.splice(i, 1);
          },
          addField(sIndex) {
              this.sections[sIndex].fields.push({ id: null, label: '', type: 'input', options_text: '', required: false });
          },
          removeField(sIndex, fIndex) {
              this.sections[sIndex].fields.splice(fIndex, 1);
          }
      }">
    @csrf
    @if ($template->exists)
        @method('PUT')
    @endif

    <div class="bg-white shadow-sm sm:rounded-lg p-6 mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre de la plantilla</label>
        <input type="text" name="name" value="{{ old('name', $template->name) }}"
               class="w-full border-gray-300 rounded-md shadow-sm" required>
        @error('name')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror

        <label class="inline-flex items-center mt-4">
            <input type="hidden" name="active" value="0">
            <input type="checkbox" name="active" value="1"
                   {{ old('active', $template->active) ? 'checked' : '' }}
                   class="rounded border-gray-300 text-indigo-600">
            <span class="ml-2 text-sm text-gray-700">Plantilla activa (disponible para seleccionar en Ejecuciones)</span>
        </label>

        <div class="mt-6 border-t pt-5">
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Banner de la encuesta
                <span class="text-gray-400 font-normal">(opcional — JPG, PNG o WebP, 1600 × 400 px recomendado, máx. 4 MB)</span>
            </label>

            @if ($template->exists && $template->banner_path)
                <div class="mb-3">
                    <img src="{{ $template->bannerUrl() }}" alt="Banner actual"
                         class="w-full max-h-32 object-cover rounded-lg border border-gray-200">
                    <label class="inline-flex items-center mt-2">
                        <input type="checkbox" name="remove_banner" value="1"
                               class="rounded border-gray-300 text-red-600">
                        <span class="ml-2 text-xs text-red-600">Quitar banner actual</span>
                    </label>
                </div>
            @endif

            <input type="file" name="banner" accept="image/jpeg,image/png,image/webp"
                   class="w-full text-sm text-gray-700 border border-gray-300 rounded-lg p-2">
            @error('banner')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mt-6 border-t pt-5">
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Descripción de la encuesta
                <span class="text-gray-400 font-normal">(opcional — se muestra bajo el banner, antes de las preguntas)</span>
            </label>
            <textarea name="description" rows="4"
                      placeholder="Ej: Solicitamos su colaboración para evaluar la actividad realizada..."
                      class="w-full border-gray-300 rounded-md shadow-sm text-sm">{{ old('description', $template->description ?? '') }}</textarea>
            @error('description')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <template x-for="(section, sIndex) in sections" :key="sIndex">
        <div class="bg-white shadow-sm sm:rounded-lg p-6 mb-6 border border-gray-100">
            <input type="hidden" :name="`sections[${sIndex}][id]`" :value="section.id">
            <div class="flex items-start justify-between gap-4 mb-4">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Título de la sección</label>
                    <input type="text" :name="`sections[${sIndex}][title]`" x-model="section.title"
                           placeholder="Ej: De los Relatores / General"
                           class="w-full border-gray-300 rounded-md shadow-sm" required>
                </div>
                <button type="button" @click="removeSection(sIndex)"
                        class="text-red-600 hover:underline text-sm mt-6" x-show="sections.length > 1">
                    Quitar sección
                </button>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Descripción de la sección <span class="text-gray-400 font-normal">(opcional)</span>
                </label>
                <textarea :name="`sections[${sIndex}][description]`" x-model="section.description" rows="2"
                          placeholder="Ej: Marque la opción que a su criterio refleja su evaluación..."
                          class="w-full border-gray-300 rounded-md shadow-sm text-sm"></textarea>
            </div>

            <div class="space-y-3">
                <template x-for="(field, fIndex) in section.fields" :key="fIndex">
                    <div class="border border-gray-200 rounded-md p-3">
                        <input type="hidden" :name="`sections[${sIndex}][fields][${fIndex}][id]`" :value="field.id">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                            <div class="md:col-span-2">
                                <label class="block text-xs font-medium text-gray-500 mb-1">Pregunta / etiqueta</label>
                                <input type="text" :name="`sections[${sIndex}][fields][${fIndex}][label]`"
                                       x-model="field.label"
                                       class="w-full border-gray-300 rounded-md shadow-sm text-sm" required>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Tipo de campo</label>
                                <select :name="`sections[${sIndex}][fields][${fIndex}][type]`" x-model="field.type"
                                        class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                                    <template x-for="(label, value) in fieldTypes" :key="value">
                                        <option :value="value" x-text="label"></option>
                                    </template>
                                </select>
                            </div>
                            <div class="flex items-end justify-between">
                                <label class="inline-flex items-center">
                                    <input type="hidden" :name="`sections[${sIndex}][fields][${fIndex}][required]`" value="0">
                                    <input type="checkbox" :name="`sections[${sIndex}][fields][${fIndex}][required]`" value="1"
                                           x-model="field.required" class="rounded border-gray-300 text-indigo-600">
                                    <span class="ml-2 text-xs text-gray-700">Obligatorio</span>
                                </label>
                                <button type="button" @click="removeField(sIndex, fIndex)"
                                        class="text-red-600 hover:underline text-xs" x-show="section.fields.length > 1">
                                    Quitar
                                </button>
                            </div>
                        </div>

                        <div class="mt-2" x-show="field.type === 'radio' || field.type === 'select'">
                            <label class="block text-xs font-medium text-gray-500 mb-1">
                                Opciones (separadas por coma) — ej: 3, 4, 5, 6, 7
                            </label>
                            <input type="text" :name="`sections[${sIndex}][fields][${fIndex}][options_text]`"
                                   x-model="field.options_text"
                                   class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                        </div>
                    </div>
                </template>

                <button type="button" @click="addField(sIndex)"
                        class="text-indigo-600 hover:underline text-sm">
                    + Agregar campo
                </button>
            </div>
        </div>
    </template>

    <button type="button" @click="addSection()"
            class="mb-6 inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-md text-xs font-semibold uppercase tracking-widest hover:bg-gray-50">
        + Agregar sección
    </button>

    <div class="flex items-center gap-3">
        <button type="submit"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md text-xs font-semibold uppercase tracking-widest hover:bg-indigo-500">
            {{ $template->exists ? 'Guardar cambios' : 'Crear plantilla' }}
        </button>
        <a href="{{ route('survey-templates.index') }}" class="text-gray-600 text-sm hover:underline">Cancelar</a>
    </div>
</form>
