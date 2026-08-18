<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar Procedimiento</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <x-breadcrumb :items="[
                ['label' => 'Ver Norma', 'url' => route('quality.norm.index')],
                ['label' => 'Procedimientos', 'url' => route('quality.procedures.index')],
                ['label' => $procedure->name, 'url' => route('quality.procedures.show', $procedure)],
                ['label' => 'Editar'],
            ]" />

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden p-6">

                <form method="POST" action="{{ route('quality.procedures.update', $procedure) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-medium">Título</label>
                        <input type="text" name="name" value="{{ old('name', $procedure->name) }}" required
                               class="mt-1 w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        @error('name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Detalle</label>
                        <textarea name="description" rows="3"
                                  class="mt-1 w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $procedure->description) }}</textarea>
                    </div>

                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="font-semibold text-gray-900 mb-4">Control y aprobación</h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium">Encargado Revisión</label>
                                <input type="text" name="reviewer_name" value="{{ old('reviewer_name', $procedure->reviewer_name) }}"
                                       class="mt-1 w-full rounded-md border-gray-300 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium">Fecha Revisión</label>
                                <input type="date" name="review_date" value="{{ old('review_date', optional($procedure->review_date)->format('Y-m-d')) }}"
                                       class="mt-1 w-full rounded-md border-gray-300 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium">Encargado Aprobación</label>
                                <input type="text" name="approver_name" value="{{ old('approver_name', $procedure->approver_name) }}"
                                       class="mt-1 w-full rounded-md border-gray-300 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium">Fecha Aprobación</label>
                                <input type="date" name="approval_date" value="{{ old('approval_date', optional($procedure->approval_date)->format('Y-m-d')) }}"
                                       class="mt-1 w-full rounded-md border-gray-300 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium">Fecha Próxima Revisión</label>
                                <input type="date" name="next_review_date" value="{{ old('next_review_date', optional($procedure->next_review_date)->format('Y-m-d')) }}"
                                       class="mt-1 w-full rounded-md border-gray-300 text-sm">
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 pt-2">
                        <button class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md text-xs font-semibold uppercase tracking-widest hover:bg-indigo-500">
                            Guardar
                        </button>
                        <a href="{{ route('quality.procedures.show', $procedure) }}" class="text-sm text-gray-600 hover:underline">Volver</a>
                    </div>

                </form>

                <form method="POST" action="{{ route('quality.procedures.destroy', $procedure) }}"
                      class="mt-6 pt-6 border-t border-gray-200"
                      onsubmit="return confirm('¿Eliminar este procedimiento? Se eliminarán todas sus versiones. Esta acción no se puede deshacer.')">
                    @csrf
                    @method('DELETE')
                    <button class="text-sm text-red-600 hover:underline">
                        Eliminar procedimiento
                    </button>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
