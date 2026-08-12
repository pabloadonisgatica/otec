<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar {{ $nonConformity->code }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            @include('quality._nav')

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6">

                    <form method="POST" action="{{ route('quality.non-conformities.update', $nonConformity) }}" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <div>
                            <label class="block text-sm font-medium">Título</label>
                            <input type="text" name="title" value="{{ old('title', $nonConformity->title) }}" required
                                   class="mt-1 w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            @error('title') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium">Fuente</label>
                                <select name="source" class="mt-1 w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Sin especificar</option>
                                    <option value="internal_audit" @selected(old('source', $nonConformity->source) == 'internal_audit')>Auditoría interna</option>
                                    <option value="external_audit" @selected(old('source', $nonConformity->source) == 'external_audit')>Auditoría externa</option>
                                    <option value="complaint" @selected(old('source', $nonConformity->source) == 'complaint')>Reclamo</option>
                                    <option value="survey" @selected(old('source', $nonConformity->source) == 'survey')>Encuesta de satisfacción</option>
                                    <option value="other" @selected(old('source', $nonConformity->source) == 'other')>Otra</option>
                                </select>
                                @error('source') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium">Origen</label>
                                <input type="text" name="origin" value="{{ old('origin', $nonConformity->origin) }}"
                                       class="mt-1 w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                @error('origin') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium">Tipo</label>
                                <select name="nc_type" class="mt-1 w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Sin especificar</option>
                                    <option value="major" @selected(old('nc_type', $nonConformity->nc_type) == 'major')>Mayor</option>
                                    <option value="minor" @selected(old('nc_type', $nonConformity->nc_type) == 'minor')>Menor</option>
                                    <option value="observation" @selected(old('nc_type', $nonConformity->nc_type) == 'observation')>Observación</option>
                                </select>
                                @error('nc_type') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium">Descripción de la No Conformidad</label>
                            <textarea name="description" rows="3" required
                                      class="mt-1 w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $nonConformity->description) }}</textarea>
                            @error('description') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium">Evidencia objetiva</label>
                            <textarea name="objective_evidence" rows="2"
                                      class="mt-1 w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('objective_evidence', $nonConformity->objective_evidence) }}</textarea>
                            @error('objective_evidence') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium">Referencia normativa</label>
                                <input type="text" name="normative_reference" value="{{ old('normative_reference', $nonConformity->normative_reference) }}"
                                       class="mt-1 w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                @error('normative_reference') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium">Proceso / área de origen</label>
                                <input type="text" name="process" value="{{ old('process', $nonConformity->process) }}"
                                       class="mt-1 w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                @error('process') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium">Corrección (arreglo inmediato / contención)</label>
                            <textarea name="correction" rows="2"
                                      class="mt-1 w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('correction', $nonConformity->correction) }}</textarea>
                            @error('correction') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium">Investigación de causa raíz</label>
                            <textarea name="root_cause" rows="3"
                                      class="mt-1 w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('root_cause', $nonConformity->root_cause) }}</textarea>
                            @error('root_cause') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium">Fecha de detección</label>
                                <input type="date" name="detected_at" value="{{ old('detected_at', optional($nonConformity->detected_at)->format('Y-m-d')) }}" required
                                       class="mt-1 w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                @error('detected_at') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium">Responsable</label>
                                <select name="responsible_id" class="mt-1 w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Sin asignar</option>
                                    @foreach($users as $u)
                                        <option value="{{ $u->id }}" @selected(old('responsible_id', $nonConformity->responsible_id) == $u->id)>{{ $u->name }}</option>
                                    @endforeach
                                </select>
                                @error('responsible_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium">Ejecución relacionada (opcional)</label>
                            <select name="execution_id" class="mt-1 w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Sin relacionar</option>
                                @foreach($executions as $e)
                                    <option value="{{ $e->id }}" @selected(old('execution_id', $nonConformity->execution_id) == $e->id)>
                                        {{ $e->internal_code }} — {{ $e->course_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('execution_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium">Estado</label>
                            <select name="status" class="mt-1 w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                @foreach(['open' => 'Abierta', 'in_progress' => 'En proceso', 'closed' => 'Cerrada'] as $value => $label)
                                    <option value="{{ $value }}" @selected(old('status', $nonConformity->status) == $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('status') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium">Notas (opcional)</label>
                            <textarea name="notes" rows="2"
                                      class="mt-1 w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes', $nonConformity->notes) }}</textarea>
                            @error('notes') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex items-center justify-between pt-2">
                            <div class="flex items-center gap-3">
                                <button class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md text-xs font-semibold uppercase tracking-widest hover:bg-indigo-500">
                                    Guardar
                                </button>
                                <a href="{{ route('quality.non-conformities.show', $nonConformity) }}" class="text-sm text-gray-600 hover:underline">Volver</a>
                            </div>
                        </div>

                    </form>

                    <form method="POST" action="{{ route('quality.non-conformities.destroy', $nonConformity) }}"
                          class="mt-6 pt-6 border-t border-gray-200"
                          onsubmit="return confirm('¿Eliminar esta no conformidad? Esta acción no se puede deshacer.')">
                        @csrf
                        @method('DELETE')
                        <button class="text-sm text-red-600 hover:underline">
                            Eliminar no conformidad
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
