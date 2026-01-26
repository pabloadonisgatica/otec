@php
    // Blindaje total: $course siempre existe
    $course = $course ?? null;

    $v = fn($key) => old($key, $course?->$key);
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">

    <div class="md:col-span-2">
        <label class="block text-sm font-medium">Empresa</label>
        <select name="company_id" class="mt-1 w-full rounded border-gray-300" required>
            <option value="">Seleccione...</option>
            @foreach($companies as $c)
                <option value="{{ $c->id }}" @selected(old('company_id', $course?->company_id) == $c->id)>
                    {{ $c->name }}
                </option>
            @endforeach
        </select>
        @error('company_id') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium">Código SENCE (opcional)</label>
        <input name="sence_code" value="{{ $v('sence_code') }}" class="mt-1 w-full rounded border-gray-300" />
        @error('sence_code') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-medium">Nombre del curso</label>
        <input name="name" value="{{ $v('name') }}" class="mt-1 w-full rounded border-gray-300" required />
        @error('name') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-medium">Tipo de actividad</label>

        @php $at = old('activity_type', $course?->activity_type); @endphp

        <div class="mt-2 flex flex-wrap gap-4 text-sm">
            <label class="inline-flex items-center gap-2">
                <input type="radio" name="activity_type" value="curso" class="rounded border-gray-300"
                    @checked($at === 'curso')>
                <span>Curso</span>
            </label>

            <label class="inline-flex items-center gap-2">
                <input type="radio" name="activity_type" value="seminario" class="rounded border-gray-300"
                    @checked($at === 'seminario')>
                <span>Seminario</span>
            </label>
        </div>

        @error('activity_type') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-medium">Modalidad de instrucción</label>

        @php $im = old('instruction_modality', $course?->instruction_modality); @endphp

        <div class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
            <label class="inline-flex items-center gap-2">
                <input type="radio" name="instruction_modality" value="presencial" class="rounded border-gray-300"
                    @checked($im === 'presencial')>
                <span>Presencial</span>
            </label>

            <label class="inline-flex items-center gap-2">
                <input type="radio" name="instruction_modality" value="elearning_sync" class="rounded border-gray-300"
                    @checked($im === 'elearning_sync')>
                <span>E-learning Sincrónico</span>
            </label>

            <label class="inline-flex items-center gap-2">
                <input type="radio" name="instruction_modality" value="elearning_async" class="rounded border-gray-300"
                    @checked($im === 'elearning_async')>
                <span>E-learning Asincrónico</span>
            </label>

            <label class="inline-flex items-center gap-2">
                <input type="radio" name="instruction_modality" value="distance_self" class="rounded border-gray-300"
                    @checked($im === 'distance_self')>
                <span>Distancia – Autoaprendizaje</span>
            </label>
        </div>

        @error('instruction_modality') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium">Porcentaje de asistencia</label>
        <input type="number" step="0.01" min="0" max="100"
            name="attendance_percentage"
            value="{{ $v('attendance_percentage') }}"
            class="mt-1 w-full rounded border-gray-300"
            placeholder="Ej: 75.00">
        @error('attendance_percentage') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium">Nota mínima</label>
        <input type="number" step="0.01" min="0"
            name="min_grade"
            value="{{ $v('min_grade') }}"
            class="mt-1 w-full rounded border-gray-300"
            placeholder="Ej: 4.00">
        @error('min_grade') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium">Horas mínimas (e-learning / b-learning)</label>
        <input type="number" min="0"
            name="min_hours"
            value="{{ $v('min_hours') }}"
            class="mt-1 w-full rounded border-gray-300"
            placeholder="Ej: 8">
        @error('min_hours') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-medium">Fundamentación técnica</label>
        <textarea name="technical_foundation" rows="4"
            class="mt-1 w-full rounded border-gray-300"
            placeholder="Describe la necesidad y pertinencia técnica del curso...">{{ $v('technical_foundation') }}</textarea>
        @error('technical_foundation') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-medium">Población objetivo</label>
        <textarea name="target_population" rows="4"
            class="mt-1 w-full rounded border-gray-300"
            placeholder="A quién va dirigido, requisitos, perfil...">{{ $v('target_population') }}</textarea>
        @error('target_population') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-medium">Objetivos generales</label>
        <textarea name="general_objectives" rows="4"
            class="mt-1 w-full rounded border-gray-300"
            placeholder="Objetivo general de la actividad...">{{ $v('general_objectives') }}</textarea>
        @error('general_objectives') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-medium">Método o técnica de enseñanza</label>
        <textarea name="teaching_methodology" rows="4"
            class="mt-1 w-full rounded border-gray-300"
            placeholder="Metodología, estrategias, dinámica de enseñanza...">{{ $v('teaching_methodology') }}</textarea>
        @error('teaching_methodology') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
    </div>

    {{-- Horas totales: se calcularán desde la tabla de actividades --}}
    <div>
        <label class="block text-sm font-medium">Horas totales (calculadas)</label>

        {{-- Guardamos el total calculado en courses.hours --}}
        <input type="hidden" name="hours" :value="totalHours">

        <div class="mt-1 px-3 py-2 rounded border bg-gray-50 text-gray-800">
            <span class="text-sm text-gray-600">Total:</span>
            <span class="font-semibold" x-text="totalHours"></span>
            <span class="text-sm text-gray-600">horas</span>
        </div>

        <div class="mt-1 text-xs text-gray-500">
            Teóricas: <span x-text="totalTheoretical"></span> ·
            Prácticas: <span x-text="totalPractical"></span> ·
            E-learning: <span x-text="totalElearning"></span>
        </div>

        @error('hours') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium">Fecha inicio (opcional)</label>
        <input type="date" name="start_date"
            value="{{ old('start_date', $course?->start_date?->format('Y-m-d')) }}"
            class="mt-1 w-full rounded border-gray-300" />
        @error('start_date') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium">Fecha término (opcional)</label>
        <input type="date" name="end_date"
            value="{{ old('end_date', $course?->end_date?->format('Y-m-d')) }}"
            class="mt-1 w-full rounded border-gray-300" />
        @error('end_date') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium">Estado</label>
        @php $s = old('status', $course?->status ?? 'draft'); @endphp
        <select name="status" class="mt-1 w-full rounded border-gray-300" required>
            <option value="draft" @selected($s === 'draft')>Borrador</option>
            <option value="planned" @selected($s === 'planned')>Planificado</option>
            <option value="ongoing" @selected($s === 'ongoing')>En ejecución</option>
            <option value="completed" @selected($s === 'completed')>Finalizado</option>
            <option value="cancelled" @selected($s === 'cancelled')>Cancelado</option>
        </select>
        @error('status') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium">N° participantes (real)</label>
        <input type="number" min="0" name="participants_count" value="{{ $v('participants_count') }}"
            class="mt-1 w-full rounded border-gray-300" />
        @error('participants_count') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium">Valor efectivo por participante</label>
        <input type="number" min="0" name="value_per_participant" value="{{ $v('value_per_participant') }}"
            class="mt-1 w-full rounded border-gray-300" />
        @error('value_per_participant') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium">Fecha solicitud de código</label>
        <input type="date"
            name="sence_request_date"
            value="{{ old('sence_request_date', $course?->sence_request_date?->format('Y-m-d')) }}"
            class="mt-1 w-full rounded border-gray-300" />
        @error('sence_request_date') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium">Fecha expiración de código</label>
        <input type="date"
            name="sence_expiration_date"
            value="{{ old('sence_expiration_date', $course?->sence_expiration_date?->format('Y-m-d')) }}"
            class="mt-1 w-full rounded border-gray-300" />
        @error('sence_expiration_date') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-medium">Relatores</label>

        @php
            $selectedInstructors = old(
                'instructor_ids',
                ($course && $course->exists) ? $course->instructors->pluck('id')->all() : []
            );
        @endphp

        <select name="instructor_ids[]" multiple class="mt-1 w-full rounded border-gray-300 min-h-[140px]">
            @foreach(($instructors ?? []) as $ins)
                <option value="{{ $ins->id }}" @selected(in_array($ins->id, $selectedInstructors))>
                    {{ $ins->name }}
                </option>
            @endforeach
        </select>

        <p class="text-xs text-gray-500 mt-1">Ctrl (Windows) / Cmd (Mac) para seleccionar varios.</p>

        @error('instructor_ids') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
        @error('instructor_ids.*') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
    </div>

    {{-- ACTIVIDADES Y CONTENIDOS --}}
    <div class="md:col-span-2">
        <h3 class="font-semibold text-gray-800 mb-2">Actividades y contenidos</h3>

        <div class="hidden md:grid md:grid-cols-7 gap-2 mb-1 text-xs text-gray-600 font-medium">
            <div class="col-span-2">Actividad</div>
            <div class="col-span-2">Contenido</div>
            <div>Hrs. Teóricas</div>
            <div>Hrs. Prácticas</div>
            <div>Hrs. E-learning</div>
            <div class="text-right">Acción</div>
        </div>

        <template x-for="(row, index) in contents" :key="index">
            <div class="grid grid-cols-1 md:grid-cols-7 gap-2 mb-2">
                <input type="text"
                    :name="`contents[${index}][activity]`"
                    x-model="row.activity"
                    placeholder="Ej: Introducción al curso"
                    aria-label="Actividad"
                    class="rounded border-gray-300 md:col-span-2">

                <input type="text"
                    :name="`contents[${index}][content]`"
                    x-model="row.content"
                    placeholder="Ej: Conceptos básicos"
                    aria-label="Contenido"
                    class="rounded border-gray-300 md:col-span-2">

                <input type="number"
                    min="0"
                    :name="`contents[${index}][hours_theoretical]`"
                    x-model="row.hours_theoretical"
                    aria-label="Horas teóricas"
                    class="rounded border-gray-300">

                <input type="number"
                    min="0"
                    :name="`contents[${index}][hours_practical]`"
                    x-model="row.hours_practical"
                    aria-label="Horas prácticas"
                    class="rounded border-gray-300">

                <input type="number"
                    min="0"
                    :name="`contents[${index}][hours_elearning]`"
                    x-model="row.hours_elearning"
                    aria-label="Horas e-learning"
                    class="rounded border-gray-300">

                <div class="md:text-right">
                    <button type="button"
                        @click="removeContent(index)"
                        class="text-red-600 text-sm">
                        Quitar
                    </button>
                </div>
            </div>
        </template>

        <button type="button"
            @click="addContent()"
            class="mt-2 px-3 py-1 bg-gray-100 rounded text-sm hover:bg-gray-200">
            + Agregar actividad
        </button>
    </div>

</div>

