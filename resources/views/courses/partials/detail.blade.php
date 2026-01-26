<div class="p-4 sm:p-6">
    <div class="flex items-start justify-between gap-4">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">
                {{ $course->name }}
            </h3>
            <p class="text-sm text-gray-600">
                {{ $course->company?->name ?? 'Sin empresa' }} ·
                <span class="font-medium">{{ strtoupper($course->status ?? 'draft') }}</span>
            </p>
        </div>

        <button type="button"
                class="text-gray-500 hover:text-gray-700"
                @click="close()">
            ✕
        </button>
    </div>

    <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
        <div class="rounded border p-3">
            <div class="text-xs text-gray-500">Código SENCE</div>
            <div class="font-medium">{{ $course->sence_code ?: '—' }}</div>
        </div>

        <div class="rounded border p-3">
            <div class="text-xs text-gray-500">Tipo / Modalidad</div>
            <div class="font-medium">
                {{ $course->activity_type ?: '—' }} · {{ $course->instruction_modality ?: '—' }}
            </div>
        </div>

        <div class="rounded border p-3">
            <div class="text-xs text-gray-500">Fechas</div>
            <div class="font-medium">
                {{ $course->start_date?->format('d-m-Y') ?? '—' }}
                →
                {{ $course->end_date?->format('d-m-Y') ?? '—' }}
            </div>
        </div>

        <div class="rounded border p-3">
            <div class="text-xs text-gray-500">Horas totales</div>
            <div class="font-medium">{{ $course->hours ?? 0 }}</div>
        </div>

        <div class="rounded border p-3">
            <div class="text-xs text-gray-500">% asistencia / Nota mín.</div>
            <div class="font-medium">
                {{ $course->attendance_percentage ?? '—' }}% · {{ $course->min_grade ?? '—' }}
            </div>
        </div>

        <div class="rounded border p-3">
            <div class="text-xs text-gray-500">Participantes / Valor p/p</div>
            <div class="font-medium">
                {{ $course->participants_count ?? '—' }} · {{ $course->value_per_participant ?? '—' }}
            </div>
        </div>

        <div class="md:col-span-2 rounded border p-3">
            <div class="text-xs text-gray-500">Fundamentación técnica</div>
            <div class="mt-1 whitespace-pre-line">{{ $course->technical_foundation ?: '—' }}</div>
        </div>

        <div class="md:col-span-2 rounded border p-3">
            <div class="text-xs text-gray-500">Población objetivo</div>
            <div class="mt-1 whitespace-pre-line">{{ $course->target_population ?: '—' }}</div>
        </div>

        <div class="md:col-span-2 rounded border p-3">
            <div class="text-xs text-gray-500">Objetivos generales</div>
            <div class="mt-1 whitespace-pre-line">{{ $course->general_objectives ?: '—' }}</div>
        </div>

        <div class="md:col-span-2 rounded border p-3">
            <div class="text-xs text-gray-500">Metodología de enseñanza</div>
            <div class="mt-1 whitespace-pre-line">{{ $course->teaching_methodology ?: '—' }}</div>
        </div>
    </div>

    <div class="mt-6">
        <h4 class="font-semibold text-gray-900 mb-2">Relatores</h4>
        @if($course->instructors->count())
            <ul class="list-disc pl-5 text-sm text-gray-700">
                @foreach($course->instructors as $ins)
                    <li>{{ $ins->name }}</li>
                @endforeach
            </ul>
        @else
            <p class="text-sm text-gray-500">—</p>
        @endif
    </div>

    <div class="mt-6">
        <h4 class="font-semibold text-gray-900 mb-2">Actividades y contenidos</h4>

        @if($course->contents->count())
            <div class="overflow-x-auto border rounded">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="text-left px-3 py-2">Actividad</th>
                            <th class="text-left px-3 py-2">Contenido</th>
                            <th class="text-right px-3 py-2">Teóricas</th>
                            <th class="text-right px-3 py-2">Prácticas</th>
                            <th class="text-right px-3 py-2">E-learning</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($course->contents as $row)
                            <tr>
                                <td class="px-3 py-2">{{ $row->activity ?: '—' }}</td>
                                <td class="px-3 py-2">{{ $row->content ?: '—' }}</td>
                                <td class="px-3 py-2 text-right">{{ $row->hours_theoretical ?? 0 }}</td>
                                <td class="px-3 py-2 text-right">{{ $row->hours_practical ?? 0 }}</td>
                                <td class="px-3 py-2 text-right">{{ $row->hours_elearning ?? 0 }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-sm text-gray-500">—</p>
        @endif
    </div>

    <div class="mt-6 flex justify-end gap-2">
        <a href="{{ route('courses.edit', $course) }}"
           class="px-4 py-2 rounded border border-gray-300 text-gray-700 hover:bg-gray-50">
            Editar
        </a>
        <button type="button"
                class="px-4 py-2 bg-gray-900 text-white rounded hover:bg-gray-800"
                @click="close()">
            Cerrar
        </button>
    </div>
</div>
