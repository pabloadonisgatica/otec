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

    <div class="md:col-span-1">
        <label class="block text-sm font-medium text-gray-700">Cargo</label>
        <input type="text" name="position" list="position-suggestions" value="{{ $val('position') }}"
               class="mt-2 block w-full rounded-md border-gray-300 focus:border-gray-500 focus:ring-gray-500">
        <datalist id="position-suggestions">
            @foreach($positions as $p)
                <option value="{{ $p }}">
            @endforeach
        </datalist>
        @error('position') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="md:col-span-1">
        <label class="block text-sm font-medium text-gray-700">Fecha de Nacimiento</label>
        <input type="date" name="birth_date" value="{{ $val('birth_date') ? \Carbon\Carbon::parse($val('birth_date'))->format('Y-m-d') : '' }}"
               class="mt-2 block w-full rounded-md border-gray-300 focus:border-gray-500 focus:ring-gray-500">
        @error('birth_date') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="md:col-span-1">
        <label class="block text-sm font-medium text-gray-700">Género</label>
        <select name="gender" class="mt-2 block w-full rounded-md border-gray-300 focus:border-gray-500 focus:ring-gray-500">
            <option value="">Sin especificar</option>
            @foreach(['Mujer', 'Hombre', 'Otro', 'Prefiero no indicarlo'] as $g)
                <option value="{{ $g }}" @selected($val('gender') === $g)>{{ $g }}</option>
            @endforeach
        </select>
        @error('gender') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="md:col-span-1">
        <label class="block text-sm font-medium text-gray-700">Foto</label>
        @if($isEdit && $user->photo_path)
            <img src="{{ Storage::url($user->photo_path) }}" class="w-16 h-16 rounded-full object-cover mt-2 mb-2">
        @endif
        <input type="file" name="photo" accept="image/*" class="mt-1 block w-full text-sm">
        @error('photo') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="md:col-span-2 border-t border-gray-100 pt-4">

        <label class="block text-sm font-medium text-gray-700 mb-2">Documentos</label>

        @if($isEdit && !empty($user->documents))
            <div class="mb-3 space-y-1">
                @foreach($user->documents as $index => $doc)
                    <div class="flex items-center justify-between text-sm bg-gray-50 rounded px-3 py-2">
                        <span>{{ $doc['label'] ?? 'Documento' }} — {{ $doc['name'] ?? '' }}</span>
                        <span class="flex gap-2">
                            <a href="{{ route('documents.download', ['type' => 'users', 'id' => $user->id, 'index' => $index]) }}"
                               class="text-indigo-600 hover:underline text-xs">Descargar</a>
                            <form method="POST" action="{{ route('documents.delete', ['type' => 'users', 'id' => $user->id, 'index' => $index]) }}"
                                  onsubmit="return confirm('¿Eliminar este documento?')">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-600 hover:underline text-xs">Eliminar</button>
                            </form>
                        </span>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <input type="text" name="document_label" placeholder="Título del documento (ej: Cédula, Título)"
                   class="rounded-md border-gray-300 text-sm">
            <input type="file" name="documents[]" multiple class="text-sm">
        </div>
        <p class="text-xs text-gray-400 mt-1">Se agregan al guardar el formulario.</p>

    </div>

</div>
