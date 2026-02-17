<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Emitir diplomas</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6 space-y-6">

                    <form method="GET" action="{{ route('diplomas.create') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium">Curso</label>
                            <select name="course_id"
                                    class="mt-1 w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                    onchange="this.form.submit()">
                                <option value="">Selecciona un curso…</option>
                                @foreach($courses as $c)
                                    <option value="{{ $c->id }}" @selected($selectedCourseId == $c->id)>{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="text-sm text-gray-500 flex items-end">
                            Selecciona el curso para cargar participantes.
                        </div>
                    </form>

                    <form method="POST" action="{{ route('diplomas.store') }}" class="space-y-4">
                        @csrf

                        <input type="hidden" name="course_id" value="{{ $selectedCourseId }}">

                        <div>
                            <label class="block text-sm font-medium">Plantilla</label>
                            <select name="template_id"
                                    class="mt-1 w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                    required>
                                <option value="">Selecciona una plantilla…</option>
                                @foreach($templates as $t)
                                    <option value="{{ $t->id }}" @selected(old('template_id') == $t->id)>{{ $t->name }}</option>
                                @endforeach
                            </select>
                            @error('template_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium">Participantes</label>

                            @if(!$selectedCourseId)
                                <p class="mt-2 text-sm text-gray-500">Primero selecciona un curso.</p>
                            @else
                                <div class="mt-2 border rounded-md divide-y">
                                    @forelse($participants as $p)
                                        <label class="flex items-center gap-3 p-3 text-sm">
                                            <input type="checkbox" name="participant_ids[]" value="{{ $p->id }}"
                                                   class="rounded border-gray-300">
                                            <span class="flex-1">
                                                {{ $p->full_name }}
                                                @if(!empty($p->rut))
                                                    <span class="text-gray-500">({{ $p->rut }})</span>
                                                @endif
                                            </span>
                                        </label>
                                    @empty
                                        <div class="p-3 text-sm text-gray-500">No hay participantes para este curso.</div>
                                    @endforelse
                                </div>

                                @error('participant_ids') <p class="text-sm text-red-600 mt-2">{{ $message }}</p> @enderror
                            @endif
                        </div>

                        <div class="flex items-center gap-3 pt-2">
                            <button class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md text-xs font-semibold uppercase tracking-widest hover:bg-indigo-500"
                                    @disabled(!$selectedCourseId)>
                                Emitir
                            </button>

                            <a href="{{ route('diplomas.index') }}" class="text-sm text-gray-600 hover:underline">Volver</a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
