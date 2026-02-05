@php
    $isEdit = isset($user);
    $val = fn($k, $d = '') => old($k, $isEdit ? data_get($user, $k) : $d);

    $role = old('role', $isEdit ? ($user->role ?? 'user') : 'user');
    $selectedPermissions = old('permissions', $isEdit ? ($user->permissions ?? []) : []);
    if (!is_array($selectedPermissions)) $selectedPermissions = [];
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">

    <div class="md:col-span-1">
        <label class="block text-sm font-medium text-gray-700">Nombre</label>
        <input type="text" name="name" value="{{ $val('name') }}"
               class="mt-2 block w-full rounded-md border-gray-300 focus:border-gray-500 focus:ring-gray-500">
        @error('name') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="md:col-span-1">
        <label class="block text-sm font-medium text-gray-700">Email</label>
        <input type="email" name="email" value="{{ $val('email') }}"
               class="mt-2 block w-full rounded-md border-gray-300 focus:border-gray-500 focus:ring-gray-500">
        @error('email') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="md:col-span-1">
        <label class="block text-sm font-medium text-gray-700">
            Password @if($isEdit)<span class="text-xs text-gray-500">(dejar vacío para no cambiar)</span>@endif
        </label>
        <input type="password" name="password"
               class="mt-2 block w-full rounded-md border-gray-300 focus:border-gray-500 focus:ring-gray-500">
        @error('password') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="md:col-span-1">
        <label class="block text-sm font-medium text-gray-700">Rol</label>
        <select name="role"
                class="mt-2 block w-full rounded-md border-gray-300 focus:border-gray-500 focus:ring-gray-500">
            <option value="user" @selected($role === 'user')>Usuario</option>
            <option value="admin" @selected($role === 'admin')>Administrador</option>
        </select>
        @error('role') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="md:col-span-2">
        <div class="flex items-center justify-between">
            <label class="block text-sm font-medium text-gray-700">Permisos por módulo</label>
            <span class="text-xs text-gray-500">Si el rol es Administrador, se ignoran.</span>
        </div>

        <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
            @foreach($modules as $key => $label)
                <label class="flex items-center gap-2 p-2 rounded border border-gray-200 bg-white">
                    <input type="checkbox" name="permissions[]"
                           value="{{ $key }}"
                           class="rounded border-gray-300"
                           @checked(in_array($key, $selectedPermissions, true))>
                    <span class="text-sm text-gray-700">{{ $label }}</span>
                </label>
            @endforeach
        </div>

        @error('permissions') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

</div>
