<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Política de Calidad</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <x-breadcrumb :items="[
                ['label' => 'Ver Norma', 'url' => route('quality.norm.index')],
                ['label' => 'Política de Calidad'],
            ]" />

            @if (session('status'))
                <div class="mb-4 p-3 rounded bg-green-50 text-green-700">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Política --}}
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden p-6">

                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-gray-900">Política</h3>
                    <span class="text-xs text-gray-400">v{{ $policy->version }}</span>
                </div>

                <form method="POST" action="{{ route('quality.policy.update') }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium">Título 1</label>
                            <input type="text" name="title_1" value="{{ old('title_1', $policy->title_1) }}"
                                   placeholder="Ej: POLÍTICA DE CALIDAD"
                                   class="mt-1 w-full rounded-md border-gray-300 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium">Título 2</label>
                            <input type="text" name="title_2" value="{{ old('title_2', $policy->title_2) }}"
                                   placeholder="Ej: Proyecto Humano Capacitación Limitada"
                                   class="mt-1 w-full rounded-md border-gray-300 text-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Detalle</label>
                        <textarea name="detail" rows="5"
                                  class="mt-1 w-full rounded-md border-gray-300 text-sm">{{ old('detail', $policy->detail) }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium">Nombre Firma</label>
                            <input type="text" name="signature_name" value="{{ old('signature_name', $policy->signature_name) }}"
                                   class="mt-1 w-full rounded-md border-gray-300 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium">Cargo Firma</label>
                            <input type="text" name="signature_position" value="{{ old('signature_position', $policy->signature_position) }}"
                                   class="mt-1 w-full rounded-md border-gray-300 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium">Fecha Aprobación</label>
                            <input type="date" name="approval_date" value="{{ old('approval_date', optional($policy->approval_date)->format('Y-m-d')) }}"
                                   class="mt-1 w-full rounded-md border-gray-300 text-sm">
                        </div>
                    </div>

                    <div class="flex justify-end pt-2">
                        <button class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md text-xs font-semibold uppercase tracking-widest hover:bg-indigo-500">
                            Guardar Política
                        </button>
                    </div>

                </form>

            </div>

            {{-- Compromisos + Objetivos --}}
            <div class="mt-6">

                <h3 class="font-semibold text-gray-900 mb-3">Compromisos y Objetivos</h3>

                <div class="space-y-4">

                    @forelse($policy->commitments as $commitment)

                        <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden p-5" x-data="{ editingCommitment: false }">

                            {{-- Título del compromiso (con toggle de edición) --}}
                            <div x-show="!editingCommitment" class="flex items-start justify-between gap-4 mb-4">
                                <p class="text-sm font-medium text-gray-900">{{ $commitment->title }}</p>
                                <div class="flex gap-2 shrink-0">
                                    <button type="button" @click="editingCommitment = true" class="text-xs text-gray-600 hover:underline">Editar</button>
                                    <form method="POST" action="{{ route('quality.policy.commitments.destroy', $commitment) }}"
                                          onsubmit="return confirm('¿Eliminar este compromiso y todos sus objetivos?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-xs text-red-600 hover:underline">Eliminar</button>
                                    </form>
                                </div>
                            </div>

                            <form x-show="editingCommitment" x-cloak method="POST"
                                  action="{{ route('quality.policy.commitments.update', $commitment) }}"
                                  class="flex items-start gap-2 mb-4">
                                @csrf
                                @method('PUT')
                                <textarea name="title" rows="2" class="flex-1 text-sm rounded-md border-gray-300">{{ $commitment->title }}</textarea>
                                <div class="flex flex-col gap-1">
                                    <button class="text-xs px-2 py-1 rounded bg-indigo-600 text-white">Guardar</button>
                                    <button type="button" @click="editingCommitment = false" class="text-xs px-2 py-1 rounded border border-gray-300">Cancelar</button>
                                </div>
                            </form>

                            {{-- Objetivos de este compromiso --}}
                            <div class="pl-4 border-l-2 border-gray-100 space-y-2">

                                @forelse($commitment->objectives as $objective)

                                    <div x-data="{ editingObjective: false }">

                                        <div x-show="!editingObjective" class="flex items-start justify-between gap-3 text-sm">
                                            <p class="text-gray-700">• {{ $objective->title }}</p>
                                            <div class="flex gap-2 shrink-0">
                                                <button type="button" @click="editingObjective = true" class="text-xs text-gray-500 hover:underline">Editar</button>
                                                <form method="POST" action="{{ route('quality.policy.objectives.destroy', $objective) }}"
                                                      onsubmit="return confirm('¿Eliminar este objetivo?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="text-xs text-red-600 hover:underline">Eliminar</button>
                                                </form>
                                            </div>
                                        </div>

                                        <form x-show="editingObjective" x-cloak method="POST"
                                              action="{{ route('quality.policy.objectives.update', $objective) }}"
                                              class="flex items-start gap-2">
                                            @csrf
                                            @method('PUT')
                                            <textarea name="title" rows="2" class="flex-1 text-xs rounded-md border-gray-300">{{ $objective->title }}</textarea>
                                            <div class="flex flex-col gap-1">
                                                <button class="text-xs px-2 py-1 rounded bg-indigo-600 text-white">Guardar</button>
                                                <button type="button" @click="editingObjective = false" class="text-xs px-2 py-1 rounded border border-gray-300">Cancelar</button>
                                            </div>
                                        </form>

                                    </div>

                                @empty

                                    <p class="text-xs text-gray-400">Sin objetivos todavía.</p>

                                @endforelse

                                {{-- Agregar objetivo a este compromiso --}}
                                <form method="POST" action="{{ route('quality.policy.objectives.store', $commitment) }}"
                                      class="flex items-start gap-2 pt-2">
                                    @csrf
                                    <input type="text" name="title" required placeholder="Nuevo objetivo para este compromiso…"
                                           class="flex-1 text-sm rounded-md border-gray-300">
                                    <button class="px-3 py-1.5 rounded-md bg-teal-600 text-white text-xs font-semibold whitespace-nowrap">
                                        + Agregar
                                    </button>
                                </form>

                            </div>

                        </div>

                    @empty

                        <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden p-6">
                            <p class="text-sm text-gray-500">Aún no hay compromisos creados.</p>
                        </div>

                    @endforelse

                    {{-- Agregar nuevo compromiso --}}
                    <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden p-5">
                        <form method="POST" action="{{ route('quality.policy.commitments.store') }}" class="flex items-start gap-2">
                            @csrf
                            <textarea name="title" rows="2" required placeholder="Nuevo compromiso…"
                                      class="flex-1 text-sm rounded-md border-gray-300"></textarea>
                            <button class="px-4 py-2 rounded-md bg-indigo-600 text-white text-xs font-semibold uppercase tracking-widest whitespace-nowrap">
                                + Agregar compromiso
                            </button>
                        </form>
                    </div>

                </div>

            </div>

        </div>
    </div>
</x-app-layout>
