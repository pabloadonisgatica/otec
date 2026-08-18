@php
    $v = fn($key) => old($key, $record?->$key);
@endphp

<div>
    <label class="block text-sm font-medium">Nombre Registro</label>
    <input type="text" name="name" value="{{ $v('name') }}" required
           placeholder="Ej: Registro de Comunicación Interna"
           class="mt-1 w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
    @error('name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium">Usuario Aprobación</label>
        <input type="text" name="approval_user" value="{{ $v('approval_user') }}"
               class="mt-1 w-full rounded-md border-gray-300 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium">Fecha Aprobación</label>
        <input type="date" name="approval_date" value="{{ old('approval_date', optional($record?->approval_date)->format('Y-m-d')) }}"
               class="mt-1 w-full rounded-md border-gray-300 text-sm">
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium">Protección</label>
        <input type="text" name="protection" value="{{ $v('protection') }}"
               placeholder="Ej: Clave de acceso al sistema"
               class="mt-1 w-full rounded-md border-gray-300 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium">Almacenamiento</label>
        <input type="text" name="storage_location" value="{{ $v('storage_location') }}"
               placeholder="Ej: Repositorio interno"
               class="mt-1 w-full rounded-md border-gray-300 text-sm">
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium">Tiempo Retención</label>
        <input type="text" name="retention_time" value="{{ $v('retention_time') }}"
               placeholder="Ej: Indefinido"
               class="mt-1 w-full rounded-md border-gray-300 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium">Recuperación</label>
        <input type="text" name="recovery" value="{{ $v('recovery') }}"
               placeholder="Ej: Repositorio interno"
               class="mt-1 w-full rounded-md border-gray-300 text-sm">
    </div>
</div>

<div>
    <label class="block text-sm font-medium">Disposición Final</label>
    <input type="text" name="final_disposition" value="{{ $v('final_disposition') }}"
           placeholder="Ej: No aplica / eliminación"
           class="mt-1 w-full rounded-md border-gray-300 text-sm">
</div>
