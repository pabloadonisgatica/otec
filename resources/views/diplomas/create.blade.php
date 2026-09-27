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
                            <label class="block text-sm font-medium">Ejecución</label>
                            <select name="execution_id"
                                    class="mt-1 w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                    onchange="this.form.submit()">
                                <option value="">Selecciona una ejecución…</option>
                                @foreach($executions as $e)
                                    <option value="{{ $e->id }}" @selected($selectedExecutionId == $e->id)>
                                        {{ $e->internal_code }} — {{ $e->course_name }}
                                        @if($e->start_date)
                                            ({{ \Carbon\Carbon::parse($e->start_date)->format('d-m-Y') }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="text-sm text-gray-500 flex items-end">
                            Selecciona la ejecución para cargar sus participantes.
                        </div>
                    </form>

                    <form method="POST" action="{{ route('diplomas.store') }}" class="space-y-4">
                        @csrf

                        <input type="hidden" name="execution_id" value="{{ $selectedExecutionId }}">

                        <div x-data="{ logo: '{{ old('diploma_logo_id', '') }}' }">
                            <label class="block text-sm font-medium">Logo adicional</label>
                            <p class="text-xs text-gray-500">El logo de la OTEC va siempre. Puedes agregar un segundo logo.</p>

                            <div class="mt-2 flex flex-wrap gap-3">
                                <label class="flex h-20 w-36 cursor-pointer items-center justify-center rounded-md border-2 text-sm"
                                       :class="logo === '' ? 'border-indigo-500 bg-indigo-50 text-indigo-700' : 'border-gray-200 text-gray-500 hover:border-gray-300'">
                                    <input type="radio" name="diploma_logo_id" value="" class="sr-only" x-model="logo">
                                    Ninguno
                                </label>

                                @foreach($diplomaLogos as $logo)
                                    <label class="flex h-20 w-36 cursor-pointer flex-col items-center justify-center gap-1 rounded-md border-2 p-2"
                                           :class="logo === '{{ $logo->id }}' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 hover:border-gray-300'">
                                        <input type="radio" name="diploma_logo_id" value="{{ $logo->id }}" class="sr-only" x-model="logo">
                                        <img src="{{ $logo->url() }}" alt="{{ $logo->name }}" class="max-h-10 max-w-28 object-contain">
                                        <span class="text-xs text-gray-600">{{ $logo->name }}</span>
                                    </label>
                                @endforeach
                            </div>

                            @if($diplomaLogos->isEmpty())
                                <p class="mt-2 text-xs text-gray-500">
                                    No hay logos adicionales cargados. Se agregan en
                                    <a href="{{ route('settings.index') }}" class="underline">Configuración → Diplomas → Logos</a>.
                                </p>
                            @endif

                            @error('diploma_logo_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <div class="flex items-center justify-between">
                                <label class="block text-sm font-medium">Participantes</label>

                                @if($selectedExecutionId && $participants->isNotEmpty())
                                    <label class="flex items-center gap-2 text-xs text-gray-600">
                                        <input
                                            type="checkbox"
                                            onclick="document.querySelectorAll('.participant-checkbox').forEach(function(cb){ if(!cb.disabled){ cb.checked = this.checked; } }, this)"
                                            class="rounded border-gray-300">
                                        Seleccionar todos
                                    </label>
                                @endif
                            </div>

                            @if(!$selectedExecutionId)
                                <p class="mt-2 text-sm text-gray-500">Primero selecciona una ejecución.</p>
                            @else
                                <div class="mt-2 border rounded-md divide-y">
                                    @forelse($participants as $p)
                                        @php
                                            $alreadyIssued = $alreadyIssuedIds->contains($p->id);
                                            $passed = $passedIds->contains($p->id);
                                        @endphp
                                        <label class="flex items-center gap-3 p-3 text-sm {{ $alreadyIssued ? 'opacity-50' : '' }}">
                                            <input type="checkbox" name="participant_ids[]" value="{{ $p->id }}"
                                                   @disabled($alreadyIssued)
                                                   @checked($passed && !$alreadyIssued)
                                                   class="participant-checkbox rounded border-gray-300">
                                            <span class="flex-1">
                                                {{ $p->first_name }} {{ $p->last_name }}
                                                @if(!empty($p->rut))
                                                    <span class="text-gray-500">({{ $p->rut }})</span>
                                                @endif
                                            </span>
                                            @if($alreadyIssued)
                                                <span class="text-xs text-green-600 font-medium">Ya emitido</span>
                                            @elseif($passed)
                                                <span class="text-xs text-indigo-600 font-medium">Aprobó</span>
                                            @endif
                                        </label>
                                    @empty
                                        <div class="p-3 text-sm text-gray-500">Esta ejecución no tiene participantes.</div>
                                    @endforelse
                                </div>

                                <p class="mt-2 text-xs text-gray-400">
                                    Se pre-seleccionan quienes tienen nota registrada igual o superior a la mínima del curso. Puedes ajustar la selección libremente.
                                </p>

                                @error('participant_ids') <p class="text-sm text-red-600 mt-2">{{ $message }}</p> @enderror
                            @endif
                        </div>

                        <div class="flex items-center gap-3 pt-2">
                            <button class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md text-xs font-semibold uppercase tracking-widest hover:bg-indigo-500"
                                    @disabled(!$selectedExecutionId)>
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
