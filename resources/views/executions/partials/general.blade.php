<x-ui.section>

    <x-slot:header>

        <x-ui.section-header
            title="Datos generales"
            subtitle="Información general de la ejecución" />

    </x-slot:header>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        <div>
            <p class="text-xs uppercase text-gray-500">Curso</p>
            <p class="font-semibold">{{ $execution->course_name }}</p>
        </div>

        <div>
            <p class="text-xs uppercase text-gray-500">Empresa</p>
            <p class="font-semibold">{{ $execution->company->name ?? '—' }}</p>
        </div>

        <div>
            <p class="text-xs uppercase text-gray-500">Modalidad</p>
            <p class="font-semibold">{{ $execution->modality }}</p>
        </div>

        <div>
            <p class="text-xs uppercase text-gray-500">Fecha inicio</p>
            <p class="font-semibold">
                {{ \Carbon\Carbon::parse($execution->start_date)->format('d-m-Y') }}
            </p>
        </div>

        <div>
            <p class="text-xs uppercase text-gray-500">Fecha término</p>
            <p class="font-semibold">
                {{ $execution->end_date
                    ? \Carbon\Carbon::parse($execution->end_date)->format('d-m-Y')
                    : '—' }}
            </p>
        </div>

        <div>
            <p class="text-xs uppercase text-gray-500">Estado</p>
            <p class="font-semibold">
                {{ ucfirst(str_replace('_',' ',$execution->status)) }}
            </p>
        </div>

        <div>
            <p class="text-xs uppercase text-gray-500">Tipo</p>
            <p class="font-semibold">
                {{ ucfirst($execution->type) }}
            </p>
        </div>

        <div>
            <p class="text-xs uppercase text-gray-500">Evaluación</p>
            <p class="font-semibold">
                {{ $execution->evaluation_type == 'grade' ? 'Nota' : 'Porcentaje' }}
            </p>
        </div>

    </div>

</x-ui.section>