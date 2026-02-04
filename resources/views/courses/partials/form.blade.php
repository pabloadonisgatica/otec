@php
    $course = $course ?? null;
    $v = fn($key) => old($key, $course?->$key);

    // Modalidades seleccionadas (multi)
    $selectedModalities = old('instruction_modalities', $course?->instruction_modalities ?? []);
    if (!is_array($selectedModalities)) $selectedModalities = [];

    $selected = old('instruction_modalities', $course->instruction_modalities ?? []);
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">

    {{-- TIPO DE CURSO --}}
    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700">Tipo de curso</label>

        <div class="mt-2 flex gap-6">
            <label class="inline-flex items-center gap-2">
                <input type="radio" name="course_type" value="sence"
                       @checked(old('course_type', $course->course_type ?? null) === 'sence')
                >
                <span>SENCE</span>
            </label>

            <label class="inline-flex items-center gap-2">
                <input type="radio" name="course_type" value="licitacion"
                       @checked(old('course_type', $course->course_type ?? null) === 'licitacion')
                >
                <span>Licitación pública</span>
            </label>

            <label class="inline-flex items-center gap-2">
                <input type="radio" name="course_type" value="privado"
                       @checked(old('course_type', $course->course_type ?? null) === 'privado')
                >
                <span>Privado</span>
            </label>
        </div>

        @error('course_type')
            <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
        @enderror
    </div>

    {{-- FOLIO --}}
    <div>
        <label class="block text-sm font-medium text-gray-700">Folio</label>

        <input
            type="text"
            value="{{ $course->folio ?? 'Se asignará automáticamente al guardar' }}"
            class="mt-1 w-full rounded border-gray-300 bg-gray-100 text-gray-700"
            readonly
        >
    </div>

    {{-- CÓDIGO SENCE --}}
    <div>
        <label class="block text-sm font-medium">Código SENCE (opcional)</label>
        <input name="sence_code"
               value="{{ $v('sence_code') }}"
               class="mt-1 w-full rounded border-gray-300" />
        @error('sence_code') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
    </div>

    {{-- NOMBRE --}}
    <div class="md:col-span-2">
        <label class="block text-sm font-medium">Nombre del curso</label>
        <input name="name"
               value="{{ $v('name') }}"
               class="mt-1 w-full rounded border-gray-300"
               required />
        @error('name') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
    </div>

    {{-- MODALIDAD MÚLTIPLE --}}
    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700">Modalidad de instrucción (múltiple)</label>

        <div class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-3">
            <label class="inline-flex items-center gap-2">
                <input type="checkbox" name="instruction_modalities[]" value="presencial"
                       @checked(in_array('presencial', $selected))>
                <span>Presencial</span>
            </label>

            <label class="inline-flex items-center gap-2">
                <input type="checkbox" name="instruction_modalities[]" value="elearning_sync"
                       @checked(in_array('elearning_sync', $selected))>
                <span>E-learning Sincrónico</span>
            </label>

            <label class="inline-flex items-center gap-2">
                <input type="checkbox" name="instruction_modalities[]" value="elearning_async"
                       @checked(in_array('elearning_async', $selected))>
                <span>E-learning Asincrónico</span>
            </label>

            <label class="inline-flex items-center gap-2">
                <input type="checkbox" name="instruction_modalities[]" value="distance_self"
                       @checked(in_array('distance_self', $selected))>
                <span>Distancia – Autoaprendizaje</span>
            </label>
        </div>

        @error('instruction_modalities')
            <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
        @enderror
    </div>

    {{-- ASISTENCIA / NOTA --}}
    <div>
        <label class="block text-sm font-medium">Porcentaje de asistencia</label>
        <input type="number" step="0.01" min="0" max="100"
               name="attendance_percentage"
               value="{{ $v('attendance_percentage') }}"
               class="mt-1 w-full rounded border-gray-300">
        @error('attendance_percentage') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium">Nota mínima</label>
        <input type="number" step="0.01" min="0"
               name="min_grade"
               value="{{ $v('min_grade') }}"
               class="mt-1 w-full rounded border-gray-300">
        @error('min_grade') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
    </div>

    {{-- FUNDAMENTACIÓN TÉCNICA --}}
    <div>
        <label class="block text-sm font-medium text-gray-700">
            Fundamentación técnica
        </label>
        <textarea
            name="technical_foundation"
            rows="4"
            class="mt-1 w-full rounded border-gray-300"
        >{{ old('technical_foundation', $course->technical_foundation ?? '') }}</textarea>
    </div>

    {{-- POBLACIÓN OBJETIVO --}}
    <div>
        <label class="block text-sm font-medium text-gray-700">
            Población objetivo
        </label>
        <textarea
            name="target_population"
            rows="4"
            class="mt-1 w-full rounded border-gray-300"
        >{{ old('target_population', $course->target_population ?? '') }}</textarea>
    </div>

    {{-- OBJETIVOS GENERALES --}}
    <div>
        <label class="block text-sm font-medium text-gray-700">
            Objetivos generales
        </label>
        <textarea
            name="general_objectives"
            rows="4"
            class="mt-1 w-full rounded border-gray-300"
        >{{ old('general_objectives', $course->general_objectives ?? '') }}</textarea>
    </div>

    {{-- MÉTODO O TÉCNICA DE ENSEÑANZA --}}
    <div>
        <label class="block text-sm font-medium text-gray-700">
            Método o técnica de enseñanza
        </label>

        <textarea
            name="teaching_methodology"
            rows="4"
            class="mt-1 w-full rounded border-gray-300"
        >{{ old('teaching_methodology', $course->teaching_methodology ?? '') }}</textarea>
    </div>

    {{-- FECHAS (OCULTAS SI AUTOAPRENDIZAJE)
    <div x-show="!hasSelfLearning" x-transition>
        <label class="block text-sm font-medium">Fecha inicio</label>
        <input type="date"
               name="start_date"
               value="{{ old('start_date', $course?->start_date?->format('Y-m-d')) }}"
               class="mt-1 w-full rounded border-gray-300" />
        @error('start_date') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
    </div>

    <div x-show="!hasSelfLearning" x-transition>
        <label class="block text-sm font-medium">Fecha término</label>
        <input type="date"
               name="end_date"
               value="{{ old('end_date', $course?->end_date?->format('Y-m-d')) }}"
               class="mt-1 w-full rounded border-gray-300" />
        @error('end_date') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
    </div>

     APROBACIÓN SENCE
    <div class="md:col-span-2" x-show="courseType === 'sence'" x-transition>
        <label class="block text-sm font-medium">Fecha aprobación SENCE</label>
        <input type="date"
               name="sence_approval_date"
               x-model="senceApprovalDate"
               value="{{ old('sence_approval_date', $course?->sence_approval_date?->format('Y-m-d')) }}"
               class="mt-1 w-full rounded border-gray-300" />

        <p class="mt-2 text-xs text-gray-600">
            Caducidad: <span class="font-medium" x-text="senceExpiryText"></span>
            <span class="text-gray-500">(4 años)</span>
        </p>

        @error('sence_approval_date') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
    </div>--}}
    
    {{-- ACTIVIDADES --}}
    <div class="md:col-span-2">
        <h3 class="font-semibold text-gray-800 mb-2">Actividades y contenidos</h3>

        {{-- Encabezados --}}
        <div class="hidden md:grid grid-cols-5 gap-2 mb-2 text-sm font-semibold text-gray-700">
            <div>Actividades</div>
            <div>Contenidos</div>
            <div>Teórico</div>
            <div>Práctico</div>
            <div>E-learning</div>
        </div>

        <template x-for="(row, index) in contents" :key="index">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-2 mb-2 items-start">

                {{-- ACTIVIDAD --}}
                <textarea
                    :name="`contents[${index}][activity]`"
                    x-model="row.activity"
                    rows="6"
                    placeholder="Actividad"
                    class="rounded border-gray-300 w-full"
                ></textarea>

                {{-- CONTENIDO --}}
                <textarea
                    :name="`contents[${index}][content]`"
                    x-model="row.content"
                    rows="6"
                    placeholder="Contenido"
                    class="rounded border-gray-300 w-full"
                ></textarea>

                {{-- HORAS TEÓRICAS --}}
                <input
                    type="number"
                    step="0.25"
                    min="0"
                    :name="`contents[${index}][hours_theoretical]`"
                    x-model="row.hours_theoretical"
                    class="rounded border-gray-300 w-full"
                >

                {{-- HORAS PRÁCTICAS --}}
                <input
                    type="number"
                    step="0.25"
                    min="0"
                    :name="`contents[${index}][hours_practical]`"
                    x-model="row.hours_practical"
                    class="rounded border-gray-300 w-full"
                >

                {{-- HORAS E-LEARNING + BOTÓN QUITAR --}}
                <div class="flex gap-2 items-start">
                    <input
                        type="number"
                        step="0.25"
                        min="0"
                        :name="`contents[${index}][hours_elearning]`"
                        x-model="row.hours_elearning"
                        class="rounded border-gray-300 w-full"
                    >

                    {{-- Quitar solo desde la segunda fila --}}
                    <button
                        type="button"
                        x-show="index > 0"
                        @click="removeContent(index)"
                        class="px-2 py-1 text-sm rounded bg-red-100 text-red-700 hover:bg-red-200"
                    >
                        ✕
                    </button>
                </div>

                {{-- HORAS TOTALES --}}
            </div>
        </template>

        <button type="button"
                @click="addContent()"
                class="px-4 py-2 rounded bg-gray-100 hover:bg-gray-200">
            + Agregar actividad
        </button>

        <div class="md:col-span-12 pt-6">
            <label class="block text-sm font-medium">Horas totales (calculadas)</label>
            <input type="hidden" name="hours" :value="totalHours">

            <div class="mt-1 px-3 py-2 rounded border bg-gray-50 text-gray-800">
                <span class="font-semibold" x-text="totalHours.toFixed(2)"></span> horas
            </div>
        </div>
    </div>

</div>
