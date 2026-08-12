<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $nonConformity->code }} — {{ $nonConformity->title }}
                </h2>
            </div>

            <div class="flex items-center gap-3">
                @php
                    $badge = match($nonConformity->status) {
                        'open' => 'bg-red-50 text-red-700 border border-red-200',
                        'in_progress' => 'bg-amber-50 text-amber-700 border border-amber-200',
                        'closed' => 'bg-green-50 text-green-700 border border-green-200',
                    };
                    $label = match($nonConformity->status) {
                        'open' => 'Abierta',
                        'in_progress' => 'En proceso',
                        'closed' => 'Cerrada',
                    };
                @endphp
                <span class="px-3 py-1 rounded-full text-xs font-medium {{ $badge }}">{{ $label }}</span>

                <a href="{{ route('quality.non-conformities.edit', $nonConformity) }}"
                   class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-md text-xs font-semibold uppercase tracking-widest hover:bg-gray-50">
                    Editar
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @include('quality._nav')

            @if (session('status'))
                <div class="p-3 rounded bg-green-50 text-green-700">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden p-6 space-y-4">

                <div>
                    <p class="text-sm text-gray-500">Descripción</p>
                    <p class="text-gray-900">{{ $nonConformity->description }}</p>
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Fuente</p>
                        <p class="text-gray-900">
                            {{ match($nonConformity->source) {
                                'internal_audit' => 'Auditoría interna',
                                'external_audit' => 'Auditoría externa',
                                'complaint' => 'Reclamo',
                                'survey' => 'Encuesta de satisfacción',
                                'other' => 'Otra',
                                default => '—',
                            } }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Origen</p>
                        <p class="text-gray-900">{{ $nonConformity->origin ?? '—' }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Tipo</p>
                        <p class="text-gray-900">
                            {{ match($nonConformity->nc_type) {
                                'major' => 'Mayor',
                                'minor' => 'Menor',
                                'observation' => 'Observación',
                                default => '—',
                            } }}
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Proceso / área</p>
                        <p class="text-gray-900">{{ $nonConformity->process ?? '—' }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Referencia normativa</p>
                        <p class="text-gray-900">{{ $nonConformity->normative_reference ?? '—' }}</p>
                    </div>
                </div>

                @if($nonConformity->objective_evidence)
                    <div>
                        <p class="text-sm text-gray-500">Evidencia objetiva</p>
                        <p class="text-gray-900 whitespace-pre-line">{{ $nonConformity->objective_evidence }}</p>
                    </div>
                @endif

                @if($nonConformity->correction)
                    <div>
                        <p class="text-sm text-gray-500">Corrección (arreglo inmediato)</p>
                        <p class="text-gray-900 whitespace-pre-line">{{ $nonConformity->correction }}</p>
                    </div>
                @endif

                @if($nonConformity->root_cause)
                    <div>
                        <p class="text-sm text-gray-500">Investigación de causa raíz</p>
                        <p class="text-gray-900 whitespace-pre-line">{{ $nonConformity->root_cause }}</p>
                    </div>
                @endif

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Ejecución relacionada</p>
                        <p class="text-gray-900">
                            @if($nonConformity->execution)
                                <a href="{{ route('executions.show', $nonConformity->execution) }}" class="text-indigo-600 hover:underline">
                                    {{ $nonConformity->execution->internal_code }} — {{ $nonConformity->execution->course_name }}
                                </a>
                            @else
                                —
                            @endif
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Responsable</p>
                        <p class="text-gray-900">{{ $nonConformity->responsible->name ?? '—' }}</p>
                    </div>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Fecha de detección</p>
                    <p class="text-gray-900">{{ optional($nonConformity->detected_at)->format('d-m-Y') }}</p>
                </div>

                @if($nonConformity->notes)
                    <div>
                        <p class="text-sm text-gray-500">Notas</p>
                        <p class="text-gray-900 whitespace-pre-line">{{ $nonConformity->notes }}</p>
                    </div>
                @endif

                @if($nonConformity->isClosed())
                    <div class="text-sm text-green-700">
                        Cerrada el {{ optional($nonConformity->closed_at)->format('d-m-Y H:i') }}
                    </div>
                @endif

            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden p-6">

                <h3 class="font-semibold text-gray-900 mb-4">
                    Acciones Correctivas / Preventivas
                </h3>

                @forelse($nonConformity->actions as $action)

                    <div class="border border-gray-200 rounded-lg p-4 mb-4">

                        <div class="flex items-start justify-between gap-4 mb-3">
                            <div>
                                <span class="text-xs font-medium uppercase px-2 py-0.5 rounded-full {{ $action->type === 'corrective' ? 'bg-blue-50 text-blue-700' : 'bg-purple-50 text-purple-700' }}">
                                    {{ $action->type === 'corrective' ? 'Correctiva' : 'Preventiva' }}
                                </span>
                                <p class="text-gray-900 mt-2">{{ $action->description }}</p>
                            </div>

                            <form method="POST" action="{{ route('quality.non-conformities.actions.destroy', [$nonConformity, $action]) }}"
                                  onsubmit="return confirm('¿Eliminar esta acción?')">
                                @csrf
                                @method('DELETE')
                                <button class="text-xs text-red-600 hover:underline whitespace-nowrap">Eliminar</button>
                            </form>
                        </div>

                        <form method="POST" action="{{ route('quality.non-conformities.actions.update', [$nonConformity, $action]) }}"
                              class="space-y-4">
                            @csrf
                            @method('PUT')

                            <input type="hidden" name="type" value="{{ $action->type }}">
                            <input type="hidden" name="description" value="{{ $action->description }}">

                            <div class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
                                <div>
                                    <label class="block text-xs text-gray-500">Responsable</label>
                                    <select name="responsible_id" class="mt-1 w-full text-sm rounded-md border-gray-300">
                                        <option value="">Sin asignar</option>
                                        @foreach($users as $u)
                                            <option value="{{ $u->id }}" @selected($action->responsible_id == $u->id)>{{ $u->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs text-gray-500">Vencimiento</label>
                                    <input type="date" name="due_date" value="{{ optional($action->due_date)->format('Y-m-d') }}"
                                           class="mt-1 w-full text-sm rounded-md border-gray-300">
                                </div>

                                <div>
                                    <label class="block text-xs text-gray-500">Estado</label>
                                    <select name="status" class="mt-1 w-full text-sm rounded-md border-gray-300">
                                        @foreach(['pending' => 'Pendiente', 'in_progress' => 'En proceso', 'completed' => 'Completada'] as $value => $label)
                                            <option value="{{ $value }}" @selected($action->status == $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <button class="px-3 py-2 rounded-md bg-gray-800 text-white text-xs hover:bg-gray-700">
                                    Actualizar
                                </button>
                            </div>

                            <div>
                                <label class="block text-xs text-gray-500">Evidencia / seguimiento</label>
                                <textarea name="evidence" rows="2" class="mt-1 w-full text-sm rounded-md border-gray-300">{{ $action->evidence }}</textarea>
                            </div>

                            <div class="border-t border-gray-100 pt-3">
                                <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Implementación</p>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                    <div>
                                        <label class="block text-xs text-gray-500">Fecha de implementación</label>
                                        <input type="date" name="implemented_at" value="{{ optional($action->implemented_at)->format('Y-m-d') }}"
                                               class="mt-1 w-full text-sm rounded-md border-gray-300">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-500">Verificación de implementación</label>
                                        <input type="date" name="implementation_verified_at" value="{{ optional($action->implementation_verified_at)->format('Y-m-d') }}"
                                               class="mt-1 w-full text-sm rounded-md border-gray-300">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-500">Nombre del verificador</label>
                                        <input type="text" name="verifier_name" value="{{ $action->verifier_name }}"
                                               class="mt-1 w-full text-sm rounded-md border-gray-300">
                                    </div>
                                </div>
                            </div>

                            <div class="border-t border-gray-100 pt-3">
                                <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Verificación de eficacia</p>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                    <div>
                                        <label class="block text-xs text-gray-500">Fecha de verificación</label>
                                        <input type="date" name="effectiveness_verified_at" value="{{ optional($action->effectiveness_verified_at)->format('Y-m-d') }}"
                                               class="mt-1 w-full text-sm rounded-md border-gray-300">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-500">¿Satisfactoria?</label>
                                        <select name="effectiveness_satisfactory" class="mt-1 w-full text-sm rounded-md border-gray-300">
                                            <option value="" @selected(is_null($action->effectiveness_satisfactory))>Sin evaluar</option>
                                            <option value="1" @selected($action->effectiveness_satisfactory === true)>Sí</option>
                                            <option value="0" @selected($action->effectiveness_satisfactory === false)>No</option>
                                        </select>
                                    </div>
                                    <div class="md:col-span-1"></div>
                                </div>
                                <div class="mt-3">
                                    <label class="block text-xs text-gray-500">Notas de la verificación</label>
                                    <textarea name="effectiveness_notes" rows="2" class="mt-1 w-full text-sm rounded-md border-gray-300">{{ $action->effectiveness_notes }}</textarea>
                                </div>
                            </div>
                        </form>

                        @if($action->isCompleted())
                            <p class="text-xs text-green-600 mt-2">
                                Completada el {{ optional($action->completed_at)->format('d-m-Y H:i') }}
                            </p>
                        @endif

                        @if(! is_null($action->effectiveness_satisfactory))
                            <p class="text-xs mt-1 {{ $action->effectiveness_satisfactory ? 'text-green-600' : 'text-red-600' }}">
                                Eficacia: {{ $action->effectiveness_satisfactory ? 'Satisfactoria' : 'No satisfactoria' }}
                                @if($action->effectiveness_verified_at)
                                    ({{ $action->effectiveness_verified_at->format('d-m-Y') }})
                                @endif
                            </p>
                        @endif

                    </div>

                @empty
                    <p class="text-sm text-gray-500 mb-4">Aún no hay acciones registradas para esta no conformidad.</p>
                @endforelse

                <div class="border-t border-gray-200 pt-4 mt-2">

                    <p class="text-sm font-medium text-gray-900 mb-3">Agregar acción</p>

                    <form method="POST" action="{{ route('quality.non-conformities.actions.store', $nonConformity) }}" class="space-y-3">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-xs text-gray-500">Tipo</label>
                                <select name="type" required class="mt-1 w-full text-sm rounded-md border-gray-300">
                                    <option value="corrective">Correctiva</option>
                                    <option value="preventive">Preventiva</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs text-gray-500">Responsable</label>
                                <select name="responsible_id" class="mt-1 w-full text-sm rounded-md border-gray-300">
                                    <option value="">Sin asignar</option>
                                    @foreach($users as $u)
                                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs text-gray-500">Vencimiento</label>
                                <input type="date" name="due_date" class="mt-1 w-full text-sm rounded-md border-gray-300">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs text-gray-500">Descripción</label>
                            <textarea name="description" rows="2" required class="mt-1 w-full text-sm rounded-md border-gray-300"></textarea>
                            @error('description') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <button class="px-4 py-2 rounded-md bg-indigo-600 text-white text-xs font-semibold uppercase tracking-widest hover:bg-indigo-500">
                            + Agregar acción
                        </button>

                    </form>

                </div>

            </div>

            <a href="{{ route('quality.non-conformities.index') }}" class="text-sm text-gray-600 hover:underline">
                ← Volver al listado
            </a>

        </div>
    </div>
</x-app-layout>
