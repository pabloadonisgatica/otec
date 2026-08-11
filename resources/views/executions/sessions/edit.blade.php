<x-app-layout>

    <div class="max-w-3xl mx-auto mt-6">

        <x-ui.section>

            <x-slot:header>

                <x-ui.section-header
                    title="Editar sesión"
                    subtitle="Modifica la fecha y horario de la sesión." />

            </x-slot:header>

            <form
                method="POST"
                action="{{ route('executions.sessions.update', [$execution, $session]) }}"
                class="space-y-6">

                @csrf
                @method('PUT')

                <div class="grid md:grid-cols-2 gap-6">

                    <div>

                        <label class="block text-sm font-medium mb-2">
                            Fecha
                        </label>

                        <input
                            type="date"
                            name="session_date"
                            value="{{ old('session_date', $session->session_date) }}"
                            class="w-full rounded-lg border-gray-300">

                    </div>

                    <div>

                        <label class="block text-sm font-medium mb-2">
                            Horas
                        </label>

                        <input
                            type="number"
                            step="0.5"
                            min="0.5"
                            name="hours"
                            value="{{ old('hours', $session->hours) }}"
                            class="w-full rounded-lg border-gray-300">

                    </div>

                    <div>

                        <label class="block text-sm font-medium mb-2">
                            Hora inicio
                        </label>

                        <input
                            type="time"
                            name="start_time"
                            value="{{ old('start_time', $session->start_time ? substr($session->start_time, 0, 5) : '') }}"
                            class="w-full rounded-lg border-gray-300">

                    </div>

                </div>

                <p class="text-sm text-gray-500">
                    La hora de término se calcula automáticamente a partir de la hora de inicio y las horas de la sesión.
                </p>

                <div class="flex justify-between pt-6 border-t">

                    <a
                        href="{{ route('executions.show', [$execution, 'tab' => 'sessions']) }}"
                        class="px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-50">

                        ← Volver

                    </a>

                    <button
                        class="px-5 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">

                        Guardar cambios

                    </button>

                </div>

            </form>

        </x-ui.section>

    </div>


</x-app-layout>