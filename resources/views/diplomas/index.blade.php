<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Diplomas emitidos</h2>

            <div class="flex gap-2">
                <a href="{{ route('diplomas.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md text-xs font-semibold uppercase tracking-widest hover:bg-indigo-500">
                    Emitir diplomas
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6">

                    {{-- Filtros --}}
                    <form method="GET" action="{{ route('diplomas.index') }}" class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center">
                        <input
                            type="text"
                            name="q"
                            value="{{ $search }}"
                            placeholder="Buscar por participante, RUT, código o ejecución…"
                            class="w-full sm:max-w-md rounded-md border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">

                        <select name="course_id" onchange="this.form.submit()"
                                class="w-full sm:w-72 rounded-md border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Todos los cursos</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" @selected($courseId == $course->id)>{{ $course->name }}</option>
                            @endforeach
                        </select>

                        @if($search || $courseId)
                            <a href="{{ route('diplomas.index') }}" class="text-sm text-gray-600 hover:underline whitespace-nowrap">Limpiar filtros</a>
                        @endif
                    </form>

                    {{-- Descargar todo lo que muestra el filtro actual --}}
                    @if(($search || $courseId) && $diplomas->total() > 0)
                        <div class="mb-4">
                            @if($diplomas->total() <= $maxDownload)
                                <form method="POST" action="{{ route('diplomas.download') }}"
                                      x-data="{ busy: false }" @submit="busy = true; setTimeout(() => busy = false, 8000)">
                                    @csrf
                                    <input type="hidden" name="q" value="{{ $search }}">
                                    <input type="hidden" name="course_id" value="{{ $courseId }}">
                                    <button type="submit" :disabled="busy"
                                            class="inline-flex items-center px-3 py-2 border border-indigo-200 rounded-md text-xs font-semibold uppercase tracking-widest text-indigo-700 hover:bg-indigo-50 disabled:opacity-50"
                                            x-text="busy ? 'Generando ZIP…' : 'Descargar los {{ $diplomas->total() }} del filtro (ZIP)'">
                                        Descargar los {{ $diplomas->total() }} del filtro (ZIP)
                                    </button>
                                </form>
                            @else
                                <p class="text-sm text-gray-500">
                                    El filtro tiene {{ $diplomas->total() }} diplomas; la descarga masiva admite hasta {{ $maxDownload }}.
                                    Acota la búsqueda o selecciona los que necesitas.
                                </p>
                            @endif
                        </div>
                    @endif

                    @if($errors->has('download') || $errors->has('course_id'))
                        <p class="mb-3 text-sm text-red-600">{{ $errors->first('download') ?: $errors->first('course_id') }}</p>
                    @endif

                    <div x-data="{
                            selected: [],
                            pageIds: @js($diplomas->pluck('id')->map(fn ($id) => (string) $id)),
                            get allSelected() { return this.pageIds.length > 0 && this.selected.length === this.pageIds.length; },
                            toggleAll() { this.selected = this.allSelected ? [] : [...this.pageIds]; }
                         }">

                    {{-- Acciones masivas. Los checkboxes pertenecen al formulario de eliminar (form="");
                         la descarga recibe los mismos ids como campos ocultos. --}}
                    <div x-show="selected.length > 0" x-cloak
                         class="mb-3 flex items-center justify-between rounded-md border border-gray-200 bg-gray-50 px-4 py-2">
                        <span class="text-sm text-gray-800"><span x-text="selected.length"></span> seleccionado(s)</span>

                        <div class="flex items-center gap-5">
                            <form method="POST" action="{{ route('diplomas.download') }}"
                                  x-data="{ busy: false }" @submit="busy = true; setTimeout(() => busy = false, 8000)">
                                @csrf
                                <template x-for="id in selected" :key="id">
                                    <input type="hidden" name="diploma_ids[]" :value="id">
                                </template>
                                <button type="submit" :disabled="busy"
                                        class="text-xs font-semibold uppercase tracking-widest text-indigo-700 hover:text-indigo-900 disabled:opacity-50"
                                        x-text="busy ? 'Generando ZIP…' : 'Descargar PDF (ZIP)'">
                                    Descargar PDF (ZIP)
                                </button>
                            </form>

                            <form id="bulk-delete-form" method="POST" action="{{ route('diplomas.destroy-many') }}"
                                  @submit="if (! confirm('¿Eliminar ' + selected.length + ' diploma(s)? Podrás volver a emitirlos después.')) $event.preventDefault()">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs font-semibold uppercase tracking-widest text-red-700 hover:text-red-900">
                                    Eliminar seleccionados
                                </button>
                            </form>
                        </div>
                    </div>
                    @error('diploma_ids') <p class="mb-3 text-sm text-red-600">{{ $message }}</p> @enderror

                    <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left border-b">
                                <th class="py-2 pr-3 w-8">
                                    <input type="checkbox" class="rounded border-gray-300"
                                           :checked="allSelected" @click="toggleAll()" title="Seleccionar todos en esta página">
                                </th>
                                <th class="py-2 pr-4">Código</th>
                                <th class="py-2 pr-4">Participante</th>
                                <th class="py-2 pr-4">Ejecución</th>
                                <th class="py-2 pr-4">Plantilla</th>
                                <th class="py-2 pr-4">Emitido</th>
                                <th class="py-2 pr-4"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($diplomas as $d)
                                <tr class="border-b" :class="selected.includes('{{ $d->id }}') ? 'bg-red-50/50' : ''">
                                    <td class="py-2 pr-3">
                                        <input type="checkbox" name="diploma_ids[]" value="{{ $d->id }}" form="bulk-delete-form"
                                               x-model="selected" class="rounded border-gray-300">
                                    </td>
                                    <td class="py-2 pr-4 font-mono">{{ $d->code }}</td>
                                    <td class="py-2 pr-4">
                                        {{ $d->participant->first_name ?? '' }} {{ $d->participant->last_name ?? '' }}
                                        <span class="text-gray-500 text-xs">{{ $d->participant->rut ?? '' }}</span>
                                    </td>
                                    <td class="py-2 pr-4">
                                        {{ $d->execution->internal_code ?? '-' }}
                                        <span class="text-gray-500">{{ $d->execution->course_name ?? '' }}</span>
                                    </td>
                                    <td class="py-2 pr-4">{{ $d->template->name ?? '-' }}</td>
                                    <td class="py-2 pr-4 text-gray-600">{{ optional($d->issued_at)->format('d-m-Y') }}</td>
                                    <td class="py-2 pr-4 text-right whitespace-nowrap">
                                        <a href="{{ route('diplomas.pdf', $d) }}" target="_blank" class="text-indigo-600 hover:underline text-xs font-medium">
                                            PDF
                                        </a>
                                        <span class="text-gray-300 mx-1">|</span>
                                        <a href="{{ route('diplomas.validate', $d->code) }}" target="_blank" class="text-gray-600 hover:underline text-xs font-medium"
                                           title="Abre la página pública de validación (la misma del QR)">
                                            Validar
                                        </a>
                                        <span class="text-gray-300 mx-1">|</span>
                                        <form method="POST" action="{{ route('diplomas.destroy', $d) }}" class="inline"
                                              onsubmit="return confirm('¿Eliminar este diploma? Podrás volver a emitirlo después.')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-red-600 hover:underline text-xs font-medium">
                                                Eliminar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-6 text-center text-gray-500">
                                        @if($search || $courseId)
                                            No hay diplomas que coincidan con los filtros.
                                        @else
                                            No hay diplomas emitidos aún.
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    </div>

                    </div>

                    <div class="mt-4">
                        {{ $diplomas->links() }}
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
