@php
    $v = fn($key) => old($key, $instructor?->$key);
@endphp

<div>
    <label class="block text-sm font-medium">RUT</label>
    <input name="rut" value="{{ $v('rut') }}" class="mt-1 w-full rounded border-gray-300" required>
    @error('rut') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
</div>

<div>
    <label class="block text-sm font-medium">Nombre completo</label>
    <input name="name" value="{{ $v('name') }}" class="mt-1 w-full rounded border-gray-300" required>
    @error('name') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium">Email</label>
        <input name="email" type="email" value="{{ $v('email') }}" class="mt-1 w-full rounded border-gray-300">
        @error('email') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
    </div>
    <div>
        <label class="block text-sm font-medium">Teléfono</label>
        <input name="phone" value="{{ $v('phone') }}" class="mt-1 w-full rounded border-gray-300">
        @error('phone') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
    </div>
</div>

<div>
    <label class="block text-sm font-medium">Profesión / Título</label>
    <input name="profession" value="{{ $v('profession') }}" class="mt-1 w-full rounded border-gray-300">
    @error('profession') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
</div>

<div>
    <label class="block text-sm font-medium">Bio (opcional)</label>
    <textarea name="bio" rows="4" class="mt-1 w-full rounded border-gray-300">{{ $v('bio') }}</textarea>
    @error('bio') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
</div>

{{-- Documentos: lo hacemos en el siguiente paso (upload) --}}
<p class="text-xs text-gray-500">Documentos (CV, certificados) se agregan en el siguiente paso.</p>
