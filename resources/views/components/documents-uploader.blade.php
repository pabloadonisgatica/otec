{{--
    Carga de varios documentos en un mismo formulario: cada archivo con su propio
    tipo (CV, Título…). Se pueden agregar varias veces (botón o arrastrando) sin
    perder los anteriores; todo se envía junto al guardar el formulario.

    Envía: {name}[i][file] (archivo) y {name}[i][label] (tipo de documento).
    Uso:   <x-documents-uploader label="Documentos del relator (PDF)" />
--}}
@props([
    'name' => 'documents',
    'label' => 'Documentos (PDF)',
    'maxMb' => 5,
    'maxFiles' => 20,
])


<div class="space-y-3"
     x-data="{
         root: null,
         rows: [],
         rejected: [],
         dragging: false,
         maxBytes: {{ (int) $maxMb * 1024 * 1024 }},
         maxFiles: {{ (int) $maxFiles }},
         add(fileList) {
             this.rejected = [];
             for (const file of Array.from(fileList)) {
                 const isPdf = file.type === 'application/pdf' || /\.pdf$/i.test(file.name);
                 if (! isPdf) { this.rejected.push(file.name + ': solo se aceptan PDF'); continue; }
                 if (file.size > this.maxBytes) { this.rejected.push(file.name + ': supera los {{ (int) $maxMb }} MB'); continue; }
                 if (this.rows.length >= this.maxFiles) { this.rejected.push(file.name + ': máximo ' + this.maxFiles + ' documentos por vez'); continue; }
                 this.rows.push({ id: Date.now() + '-' + Math.random(), file, label: '' });
             }
             this.$refs.picker.value = '';
             this.$nextTick(() => this.sync());
         },
         remove(index) {
             this.rows.splice(index, 1);
             this.$nextTick(() => this.sync());
         },
         // Cada fila tiene su propio input de archivo: se le asigna el archivo elegido.
         sync() {
             this.root.querySelectorAll('input[data-document-file]').forEach((input, i) => {
                 if (! this.rows[i]) return;
                 const transfer = new DataTransfer();
                 transfer.items.add(this.rows[i].file);
                 input.files = transfer.files;
             });
         },
         size(bytes) {
             return bytes < 1048576
                 ? Math.max(1, Math.round(bytes / 1024)) + ' KB'
                 : (bytes / 1048576).toFixed(1).replace('.', ',') + ' MB';
         },
     }"
     x-init="root = $el">

    <div class="flex items-baseline justify-between gap-3">
        <label class="block text-sm font-medium text-gray-700">{{ $label }}</label>
        <span class="text-xs text-gray-500" x-show="rows.length" x-cloak
              x-text="rows.length + (rows.length === 1 ? ' documento por subir' : ' documentos por subir')"></span>
    </div>

    {{-- Zona para agregar (botón o arrastrar) --}}
    <div class="rounded-lg border-2 border-dashed p-5 text-center transition"
         :class="dragging ? 'border-indigo-500 bg-indigo-50' : 'border-gray-300'"
         @dragover.prevent="dragging = true"
         @dragleave.prevent="dragging = false"
         @drop.prevent="dragging = false; add($event.dataTransfer.files)">
        <input type="file" x-ref="picker" accept="application/pdf,.pdf" multiple class="hidden"
               @change="add($event.target.files)">
        <button type="button" @click="$refs.picker.click()"
                class="inline-flex items-center rounded-md border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50">
            + Agregar documentos
        </button>
        <p class="mt-2 text-xs text-gray-500">
            o arrástralos aquí · PDF de hasta {{ (int) $maxMb }} MB cada uno · puedes agregar varios antes de guardar
        </p>
    </div>

    <template x-if="rejected.length">
        <ul class="rounded-md border border-red-200 bg-red-50 px-4 py-2 text-xs text-red-700 space-y-0.5">
            <template x-for="message in rejected" :key="message">
                <li x-text="message"></li>
            </template>
        </ul>
    </template>

    {{-- Documentos por subir --}}
    <ul class="divide-y divide-gray-100 rounded-md border border-gray-200" x-show="rows.length" x-cloak>
        <template x-for="(row, index) in rows" :key="row.id">
            <li class="grid grid-cols-1 sm:grid-cols-12 gap-2 items-center px-3 py-2">
                <div class="sm:col-span-5 min-w-0">
                    <p class="truncate text-sm text-gray-800" x-text="row.file.name" :title="row.file.name"></p>
                    <p class="text-xs text-gray-500" x-text="size(row.file.size)"></p>
                    <input type="file" data-document-file class="hidden" :name="`{{ $name }}[${index}][file]`">
                </div>
                <div class="sm:col-span-6">
                    <input type="text" required maxlength="80"
                           :name="`{{ $name }}[${index}][label]`" x-model="row.label"
                           placeholder="Tipo de documento (ej: CV, Título, Certificado)"
                           autocomplete="off"
                           class="w-full rounded-md border-gray-300 text-sm">
                </div>
                <div class="sm:col-span-1 sm:text-right">
                    <button type="button" @click="remove(index)" class="text-xs text-red-600 hover:underline">Quitar</button>
                </div>
            </li>
        </template>
    </ul>


    @error($name)
        <p class="text-sm text-red-600">{{ $message }}</p>
    @enderror
    @foreach ($errors->get($name . '.*') as $messages)
        <p class="text-sm text-red-600">{{ $messages[0] }}</p>
    @endforeach
</div>
