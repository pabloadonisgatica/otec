@php
    $isEdit = $template && $template->exists;
@endphp

<div class="grid grid-cols-1 gap-4">
    <div>
        <label class="block text-sm font-medium">Nombre</label>
        <input type="text" name="name" value="{{ old('name', $template->name) }}"
               class="mt-1 w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" required>
        @error('name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="flex items-center gap-2">
        <input type="checkbox" name="is_active" value="1"
               class="rounded border-gray-300"
               @checked(old('is_active', $template->is_active) ? true : false)>
        <label class="text-sm">Activa</label>
    </div>

    <div>
        <label class="block text-sm font-medium">Fondo (imagen)</label>
        <input type="file" name="background" accept="image/*" class="mt-1 block w-full text-sm">

        @error('background') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror

        @if($isEdit && $template->background_path)
            <div class="mt-3 flex items-start gap-4">
                <img src="{{ asset('storage/'.$template->background_path) }}" class="h-24 rounded border" alt="Fondo">
                <label class="inline-flex items-center gap-2 text-sm">
                    <input type="checkbox" name="remove_background" value="1" class="rounded border-gray-300">
                    Quitar fondo actual
                </label>
            </div>
        @endif
    </div>

    <div>
        <label class="block text-sm font-medium">Contenido HTML (con variables)</label>
        <textarea name="content_html" rows="12"
                  class="mt-1 w-full rounded-md border-gray-300 font-mono text-sm focus:border-indigo-500 focus:ring-indigo-500"
                  required>{{ old('content_html', $template->content_html) }}</textarea>
        @error('content_html') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror

      <p class="mt-2 text-xs text-gray-500">
    @verbatim
        Por ahora guardamos HTML con placeholders (ej: <code>{{participant.full_name}}</code>).
    @endverbatim
</p>

    </div>
</div>

<div class="mt-6 flex items-center gap-3">
    <button class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md text-xs font-semibold uppercase tracking-widest hover:bg-indigo-500">
        Guardar
    </button>

    <a href="{{ route('diploma-templates.index') }}" class="text-sm text-gray-600 hover:underline">
        Volver
    </a>
</div>
