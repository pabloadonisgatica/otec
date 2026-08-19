@php
    $t = $training ?? null;
    $val = fn($k, $d = '') => old($k, $t?->$k ?? $d);
    $existingParticipants = $t?->participants->map(fn($p) => ['name' => $p->name, 'position' => $p->position])->all() ?? [];
    if (empty($existingParticipants)) {
        $existingParticipants = [['name' => '', 'position' => '']];
    }
@endphp

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium">Fecha</label>
        <input type="date" name="activity_date" value="{{ old('activity_date', optional($t?->activity_date)->format('Y-m-d')) }}" required
               class="mt-1 w-full rounded-md border-gray-300 text-sm">
        @error('activity_date') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm font-medium">Nombre de la Actividad</label>
        <input type="text" name="activity_name" value="{{ $val('activity_name') }}" required
               class="mt-1 w-full rounded-md border-gray-300 text-sm">
        @error('activity_name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>
</div>

<div>
    <label class="block text-sm font-medium">Objetivo de la Capacitación</label>
    <textarea name="objective" rows="3" class="mt-1 w-full rounded-md border-gray-300 text-sm">{{ $val('objective') }}</textarea>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium">Nombre del Relator</label>
        <input type="text" name="instructor_name" value="{{ $val('instructor_name') }}" class="mt-1 w-full rounded-md border-gray-300 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium">N° de Horas</label>
        <input type="number" min="0" name="hours" value="{{ $val('hours') }}" class="mt-1 w-full rounded-md border-gray-300 text-sm">
    </div>
</div>

<div x-data="{
    rows: {{ json_encode($existingParticipants) }}
}">
    <label class="block text-sm font-medium mb-2">Registro de Participantes</label>

    <div class="space-y-2">
        <template x-for="(row, index) in rows" :key="index">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 items-start">
                <input type="text" :name="'participant_name[' + index + ']'" x-model="row.name"
                       placeholder="Nombre" class="rounded-md border-gray-300 text-sm">
                <div class="flex gap-2">
                    <input type="text" :name="'participant_position[' + index + ']'" x-model="row.position"
                           placeholder="Cargo" class="flex-1 rounded-md border-gray-300 text-sm">
                    <button type="button" @click="rows.splice(index, 1)"
                            class="text-xs text-red-600 hover:underline shrink-0">Borrar</button>
                </div>
            </div>
        </template>
    </div>

    <button type="button" @click="rows.push({ name: '', position: '' })"
            class="mt-2 text-xs text-indigo-600 hover:underline">
        + Agregar participante
    </button>
</div>

<div class="border-t border-gray-100 pt-4">
    <p class="text-sm font-semibold text-gray-700 mb-3">Verificación de Eficacia de la Actividad</p>

    <div>
        <label class="block text-sm font-medium">Cumplimiento de Objetivo</label>
        <select name="objective_met" class="mt-1 w-full sm:w-48 rounded-md border-gray-300 text-sm">
            <option value="">Sin evaluar</option>
            <option value="1" @selected(old('objective_met', $t?->objective_met) == true)>Sí</option>
            <option value="0" @selected(old('objective_met', $t?->objective_met) === false)>No</option>
        </select>
    </div>

    <div class="mt-3">
        <label class="block text-sm font-medium">Descripción de Cumplimiento del Objetivo</label>
        <textarea name="compliance_description" rows="3" class="mt-1 w-full rounded-md border-gray-300 text-sm">{{ $val('compliance_description') }}</textarea>
    </div>

    <div class="mt-3">
        <label class="block text-sm font-medium">Requiere Acciones Adicionales</label>
        <textarea name="additional_actions" rows="2" class="mt-1 w-full rounded-md border-gray-300 text-sm">{{ $val('additional_actions') }}</textarea>
    </div>
</div>
