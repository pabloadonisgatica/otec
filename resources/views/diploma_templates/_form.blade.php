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

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

        <div>
            <label class="block text-sm font-medium">Contenido HTML (con variables)</label>
            <textarea id="content-html-editor" name="content_html" rows="24" spellcheck="false"
                      class="mt-1 w-full rounded-md border-gray-300 font-mono text-sm focus:border-indigo-500 focus:ring-indigo-500"
                      required>{{ old('content_html', $template->content_html) }}</textarea>
            @error('content_html') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror

          <p class="mt-2 text-xs text-gray-500">
        @verbatim
            Variables disponibles: <code>{{participant.full_name}}</code>, <code>{{participant.rut}}</code>,
            <code>{{course.name}}</code>, <code>{{course.hours}}</code>,
            <code>{{execution.start_date}}</code>, <code>{{execution.end_date}}</code>, <code>{{execution.company}}</code>,
            <code>{{code}}</code>, <code>{{issued_at}}</code>, <code>{{qr}}</code> (código QR de validación).
        @endverbatim
    </p>
        </div>

        <div>
            <div class="flex items-center justify-between">
                <label class="block text-sm font-medium">Vista previa en vivo (aproximada)</label>
                <button type="button" id="refresh-preview-btn" class="text-xs text-indigo-600 hover:underline">
                    🔄 Actualizar
                </button>
            </div>

            <iframe id="html-preview-frame"
                    class="mt-1 w-full border border-gray-300 rounded-md bg-white"
                    style="aspect-ratio: 11 / 8.5;"></iframe>

            <p class="mt-2 text-xs text-gray-400">
                Aproximada: el navegador no renderiza igual que el PDF final. Usa "Vista previa PDF" para el resultado exacto.
            </p>
        </div>

    </div>
</div>

<div class="mt-6 flex items-center gap-3">
    <button class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md text-xs font-semibold uppercase tracking-widest hover:bg-indigo-500">
        Guardar
    </button>

    <button
        type="submit"
        formaction="{{ route('diploma-templates.preview') }}"
        formtarget="_blank"
        formnovalidate
        onclick="var m=this.form.querySelector('input[name=_method]'); if(m){ m.disabled = true; setTimeout(function(){ m.disabled = false; }, 0); }"
        class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-md text-xs font-semibold uppercase tracking-widest hover:bg-gray-50">
        📄 Vista previa PDF
    </button>

    <a href="{{ route('diploma-templates.index') }}" class="text-sm text-gray-600 hover:underline">
        Volver
    </a>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var btn = document.getElementById('refresh-preview-btn');
        var frame = document.getElementById('html-preview-frame');
        var editor = document.getElementById('content-html-editor');

        function refreshPreview() {
            fetch('{{ route('diploma-templates.preview-html') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'content_html=' + encodeURIComponent(editor.value),
            })
                .then(function (response) { return response.text(); })
                .then(function (html) { frame.srcdoc = html; })
                .catch(function () { /* silencioso: no bloquea la edición */ });
        }

        btn.addEventListener('click', refreshPreview);

        // vista previa inicial al cargar la página
        refreshPreview();
    });
</script>
