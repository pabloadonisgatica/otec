@php
    $v = fn($key, $default = null) => old($key, $participant?->$key ?? $default);
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">

    <div>
        <label class="block text-sm font-medium text-gray-700">RUT *</label>
        <input name="rut" value="{{ $v('rut') }}"
               class="mt-1 w-full rounded-md border-gray-300"
               placeholder="12.345.678-9" required>
        @error('rut') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Empresa *</label>
        <select name="company_id" class="mt-1 w-full rounded-md border-gray-300" required>
            <option value="">Selecciona una empresa…</option>
            @foreach($companies as $company)
                <option value="{{ $company->id }}" @selected((int)$v('company_id') === $company->id)>
                    {{ $company->name }}
                </option>
            @endforeach
        </select>
        @error('company_id') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Nombre *</label>
        <input name="first_name" value="{{ $v('first_name') }}"
               class="mt-1 w-full rounded-md border-gray-300" required>
        @error('first_name') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Apellidos *</label>
        <input name="last_name" value="{{ $v('last_name') }}"
               class="mt-1 w-full rounded-md border-gray-300" required>
        @error('last_name') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Email</label>
        <input name="email" type="email" value="{{ $v('email') }}"
               class="mt-1 w-full rounded-md border-gray-300"
               placeholder="correo@dominio.cl">
        @error('email') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Teléfono</label>
        <input name="phone" value="{{ $v('phone') }}"
               class="mt-1 w-full rounded-md border-gray-300"
               placeholder="+56 9 ....">
        @error('phone') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Estado *</label>
        <select name="status" class="mt-1 w-full rounded-md border-gray-300" required>
            <option value="activo" @selected($v('status', 'activo') === 'activo')>Activo</option>
            <option value="inactivo" @selected($v('status', 'activo') === 'inactivo')>Inactivo</option>
        </select>
        @error('status') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700">Observaciones</label>
        <textarea name="notes" rows="4"
                  class="mt-1 w-full rounded-md border-gray-300"
                  placeholder="Notas internas...">{{ $v('notes') }}</textarea>
        @error('notes') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
    </div>

</div>
