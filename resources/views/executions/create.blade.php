<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Nueva ejecución
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <x-breadcrumb :items="$breadcrumbs" />

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6">

                    <form method="POST" action="{{ route('executions.store') }}">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Tipo --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Tipo
                                </label>
                                <select name="type"
                                    class="w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="cerrado">Cerrado</option>
                                    <option value="abierto">Abierto</option>
                                </select>
                            </div>
                            {{-- Curso --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Curso
                                </label>
                                <select name="course_id"
                                    class="w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="">Seleccione curso</option>
                                    @foreach($courses as $course)
                                    <option value="{{ $course->id }}"
                                        data-modalities='@json($course->instruction_modalities ?? [])'
                                        data-hours="{{ $course->hours ?? '' }}">
                                        {{ $course->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            {{-- Participantes --}}
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Participantes
                                </label>

                                <select name="participants[]"
                                    multiple
                                    class="w-full border-gray-300 rounded-md shadow-sm h-56">

                                    @foreach($participants as $participant)
                                    <option value="{{ $participant->id }}">
                                        {{ $participant->first_name }}
                                        {{ $participant->last_name }}
                                    </option>
                                    @endforeach

                                </select>

                                <p class="mt-1 text-xs text-gray-500">
                                    Mantén presionado Ctrl (o Cmd en Mac) para seleccionar múltiples participantes.
                                </p>
                            </div>
                            {{-- Modalidad (readonly) --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Modalidad
                                </label>
                                <input type="text"
                                    id="modality_display"
                                    class="w-full border-gray-300 rounded-md shadow-sm bg-gray-100"
                                    readonly>
                            </div>

                            {{-- Horas totales (readonly) --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Horas totales
                                </label>
                                <input type="text"
                                    id="hours_display"
                                    class="w-full border-gray-300 rounded-md shadow-sm bg-gray-100"
                                    readonly>
                                
                            </div>

                            {{-- Empresa --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Empresa
                                </label>
                                <select name="company_id"
                                    class="w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="">Seleccione empresa</option>
                                    @foreach($companies as $company)
                                    <option value="{{ $company->id }}">
                                        {{ $company->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Tipo evaluación --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Tipo de evaluación
                                </label>
                                <select name="evaluation_type"
                                    class="w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="percentage">Porcentaje</option>
                                    <option value="grade">Nota</option>
                                </select>
                            </div>

                            {{-- Fecha inicio --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Fecha inicio
                                </label>
                                <input type="date"
                                    name="start_date"
                                    class="w-full border-gray-300 rounded-md shadow-sm">
                            </div>
                            {{-- Horas por día --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Horas por día
                                </label>

                                <input
                                    type="number"
                                    name="hours_per_day"
                                    min="1"
                                    max="12"
                                    step="0.5"
                                    value="8"
                                    class="w-full border-gray-300 rounded-md shadow-sm">
                            </div>

                            {{-- Jornada --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Jornada
                                </label>

                            </div>

                        </div>

                        {{-- Observaciones --}}
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Observaciones
                            </label>
                            <textarea name="observations"
                                rows="3"
                                class="w-full border-gray-300 rounded-md shadow-sm"></textarea>
                        </div>

                        {{-- Botones --}}
                        <div class="mt-6 flex justify-end">
                            <a href="{{ route('executions.index') }}"
                                class="mr-3 px-4 py-2 text-sm text-gray-600 hover:text-gray-800">
                                Cancelar
                            </a>

                            <button type="submit"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-semibold hover:bg-indigo-500">
                                Guardar ejecución
                            </button>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            const select = document.querySelector('[name="course_id"]');
            const modality = document.getElementById('modality_display');
            const hours = document.getElementById('hours_display');

            if (!select) return;

            select.addEventListener("change", function() {

                const opt = this.options[this.selectedIndex];

                let modalities = [];

                try {
                    modalities = JSON.parse(opt.dataset.modalities || "[]");
                } catch (e) {
                    modalities = [];
                }

                /* mapa para nombres bonitos */
                const labels = {
                    presencial: "Presencial",
                    elearning_sync: "E-learning Sync",
                    elearning_async: "E-learning Async",
                    distance_self: "Autoaprendizaje"
                };

                const formatted = modalities.map(m => labels[m] ?? m);

                modality.value = formatted.join(" · ");
                hours.value = opt.dataset.hours ?? '';

              
                

            });

        });
    </script>
</x-app-layout>