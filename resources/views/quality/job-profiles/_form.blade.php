@php
    $p = $profile ?? null;
    $val = fn($k, $d = '') => old($k, $p?->$k ?? $d);
    $assigned = old('procedure_ids', $assignedIds ?? []);
@endphp

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium">Fecha</label>
        <input type="date" name="profile_date" value="{{ old('profile_date', optional($p?->profile_date)->format('Y-m-d')) }}"
               class="mt-1 w-full rounded-md border-gray-300 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium">Nombre del Puesto</label>
        <input type="text" name="position_name" value="{{ $val('position_name') }}" required
               class="mt-1 w-full rounded-md border-gray-300 text-sm">
        @error('position_name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>
</div>

<div>
    <label class="block text-sm font-medium">Funciones y Tareas del Puesto</label>
    <textarea name="functions" rows="3" class="mt-1 w-full rounded-md border-gray-300 text-sm">{{ $val('functions') }}</textarea>
</div>

<div>
    <label class="block text-sm font-medium">Responsabilidades</label>
    <textarea name="responsibilities" rows="4" class="mt-1 w-full rounded-md border-gray-300 text-sm">{{ $val('responsibilities') }}</textarea>
</div>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    <div>
        <label class="block text-sm font-medium">Área</label>
        <input type="text" name="area" value="{{ $val('area') }}" class="mt-1 w-full rounded-md border-gray-300 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium">Dependencia Directa De</label>
        <input type="text" name="reports_to" value="{{ $val('reports_to') }}" class="mt-1 w-full rounded-md border-gray-300 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium">Personas a su Cargo</label>
        <input type="text" name="direct_reports" value="{{ $val('direct_reports') }}" class="mt-1 w-full rounded-md border-gray-300 text-sm">
    </div>
</div>

<div>
    <label class="block text-sm font-medium">Requisitos relativos a la Educación</label>
    <textarea name="education_requirements" rows="3" class="mt-1 w-full rounded-md border-gray-300 text-sm">{{ $val('education_requirements') }}</textarea>
</div>

<div>
    <label class="block text-sm font-medium">Requisitos relativos a la Formación</label>
    <textarea name="training_requirements" rows="3" class="mt-1 w-full rounded-md border-gray-300 text-sm">{{ $val('training_requirements') }}</textarea>
</div>

<div>
    <label class="block text-sm font-medium">Requisitos relativos a las Habilidades</label>
    <textarea name="skills_requirements" rows="3" class="mt-1 w-full rounded-md border-gray-300 text-sm">{{ $val('skills_requirements') }}</textarea>
</div>

<div>
    <label class="block text-sm font-medium">Requisitos relativos a la Experiencia</label>
    <textarea name="experience_requirements" rows="3" class="mt-1 w-full rounded-md border-gray-300 text-sm">{{ $val('experience_requirements') }}</textarea>
</div>

<div class="border-t border-gray-100 pt-4">
    <label class="block text-sm font-medium mb-2">Asignar Procedimientos al Perfil de Cargo</label>

    @if($procedures->isEmpty())
        <p class="text-sm text-gray-500">Todavía no hay Procedimientos creados para asignar.</p>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
            @foreach($procedures as $procedure)
                <label class="flex items-center gap-2 text-sm">
                    <input type="checkbox" name="procedure_ids[]" value="{{ $procedure->id }}"
                           @checked(in_array($procedure->id, $assigned))>
                    {{ $procedure->name }}
                </label>
            @endforeach
        </div>
    @endif
</div>
