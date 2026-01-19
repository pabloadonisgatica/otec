@php
    $v = fn($key) => old($key, $company?->$key);
@endphp

<div>
    <label class="block text-sm font-medium">RUT</label>
    <input name="rut" value="{{ $v('rut') }}" class="mt-1 w-full rounded border-gray-300" required>
    @error('rut') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
</div>

<div>
    <label class="block text-sm font-medium">Nombre / Razón social</label>
    <input name="name" value="{{ $v('name') }}" class="mt-1 w-full rounded border-gray-300" required>
    @error('name') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
</div>

<div>
    <label class="block text-sm font-medium">Giro (opcional)</label>
    <input name="business_name" value="{{ $v('business_name') }}" class="mt-1 w-full rounded border-gray-300">
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium">Email</label>
        <input name="email" type="email" value="{{ $v('email') }}" class="mt-1 w-full rounded border-gray-300">
    </div>
    <div>
        <label class="block text-sm font-medium">Teléfono</label>
        <input name="phone" value="{{ $v('phone') }}" class="mt-1 w-full rounded border-gray-300">
    </div>
</div>

<div>
    <label class="block text-sm font-medium">Dirección</label>
    <input name="address" value="{{ $v('address') }}" class="mt-1 w-full rounded border-gray-300">
</div>

<hr class="my-4">

<p class="font-semibold">Contacto principal</p>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium">Nombre contacto</label>
        <input name="contact_name" value="{{ $v('contact_name') }}" class="mt-1 w-full rounded border-gray-300">
    </div>
    <div>
        <label class="block text-sm font-medium">Email contacto</label>
        <input name="contact_email" type="email" value="{{ $v('contact_email') }}" class="mt-1 w-full rounded border-gray-300">
    </div>
</div>

<div>
    <label class="block text-sm font-medium">Teléfono contacto</label>
    <input name="contact_phone" value="{{ $v('contact_phone') }}" class="mt-1 w-full rounded border-gray-300">
</div>
