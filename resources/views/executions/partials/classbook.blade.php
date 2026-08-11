<x-ui.section class="mt-6">

    <x-slot:header>
        <x-ui.section-header
            title="Libro de clases"
            subtitle="Asistencia y notas de los participantes de esta ejecución.">

            <x-slot:actions>
                <a
                    href="{{ route('executions.classbook.pdf', $execution) }}"
                    target="_blank"
                    class="px-4 py-2 rounded-lg border border-gray-300 text-sm font-medium hover:bg-gray-50 whitespace-nowrap">

                    🖨️ Imprimir Libro (PDF)

                </a>
            </x-slot:actions>

        </x-ui.section-header>
    </x-slot:header>

    <div x-data="{ section: 'attendance' }">

        <div class="flex gap-2 mb-6 border-b border-gray-200">

            <button
                type="button"
                @click="section = 'attendance'"
                class="px-4 py-2 border-b-2 -mb-px transition"
                :class="section === 'attendance'
                    ? 'border-indigo-600 text-indigo-600 font-semibold'
                    : 'border-transparent text-gray-500 hover:text-gray-700'">

                Asistencia

            </button>

            <button
                type="button"
                @click="section = 'grades'"
                class="px-4 py-2 border-b-2 -mb-px transition"
                :class="section === 'grades'
                    ? 'border-indigo-600 text-indigo-600 font-semibold'
                    : 'border-transparent text-gray-500 hover:text-gray-700'">

                Notas

            </button>

        </div>

        <div x-show="section === 'attendance'" x-cloak>
            @include('executions.partials.classbook-attendance')
        </div>

        <div x-show="section === 'grades'" x-cloak>
            @include('executions.partials.classbook-grades')
        </div>

    </div>

</x-ui.section>
