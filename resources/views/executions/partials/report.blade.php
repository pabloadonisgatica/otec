@php
    $counts = $reportData['counts'];
    $survey = $reportData['survey'];
    $manual = $reportData['manual'];

    $missing = collect([
        empty($reportData['instructors']) ? 'relatores asignados' : null,
        empty($reportData['place']) ? 'lugar de la ejecución' : null,
        empty($reportData['objective']) ? 'objetivo general del curso' : null,
        $counts['approved'] === null ? 'notas finales (aprobados)' : null,
        ! $survey ? 'encuesta de satisfacción' : (($survey['responses'] ?? 0) === 0 ? 'respuestas de la encuesta' : null),
    ])->filter();
@endphp

<x-ui.section class="mt-6">

    <x-slot:header>
        <x-ui.section-header
            title="Informe de la ejecución"
            subtitle="Se arma con los datos de la ejecución y la encuesta. Solo completa lo que no está en el sistema.">
            <x-slot:actions>
                <a href="{{ route('executions.report.pdf', $execution) }}"
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 whitespace-nowrap">
                    Descargar informe (PDF)
                </a>
            </x-slot:actions>
        </x-ui.section-header>
    </x-slot:header>

    @if ($missing->isNotEmpty())
        <div class="mb-6 rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
            <span class="font-semibold">Faltan datos:</span> {{ $missing->implode(', ') }}.
            El informe se puede descargar igual; esas partes aparecerán vacías o con “—”.
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

        {{-- Datos automáticos --}}
        <div>
            <h3 class="text-sm font-semibold text-gray-900 mb-3">Datos que se toman del sistema</h3>

            <dl class="divide-y divide-gray-100 text-sm">
                @foreach ([
                    'Curso' => $reportData['course_name'],
                    'Cliente' => $reportData['company_name'],
                    'Relatores' => $reportData['instructors'],
                    'Fechas' => $reportData['dates'],
                    'Lugar' => $reportData['place'],
                    'Modalidad' => $reportData['modality'],
                    'Inscritos' => $counts['enrolled'],
                    'Asistentes' => $counts['attended'] ?? '— (sin asistencia registrada)',
                    'Aprobados' => $counts['approved'] ?? '— (sin notas registradas)',
                    'Encuestas respondidas' => $survey ? $survey['responses'] : '— (sin encuesta)',
                ] as $label => $value)
                    <div class="flex justify-between gap-4 py-2">
                        <dt class="text-gray-500">{{ $label }}</dt>
                        <dd class="text-gray-900 text-right">{{ filled($value) ? $value : '—' }}</dd>
                    </div>
                @endforeach
            </dl>

            <p class="mt-3 text-xs text-gray-400">
                Para corregir estos datos, edita la ejecución, el curso o el libro de clases.
            </p>
        </div>

        {{-- Datos manuales --}}
        <div>
            <h3 class="text-sm font-semibold text-gray-900 mb-3">Datos para completar</h3>

            <form method="POST" action="{{ route('executions.report.update', $execution) }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Orden de compra</label>
                        <input type="text" name="purchase_order"
                               value="{{ old('purchase_order', $manual->purchase_order) }}"
                               class="w-full text-sm rounded-md border-gray-300">
                        @error('purchase_order') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Factura</label>
                        <input type="text" name="invoice_number"
                               value="{{ old('invoice_number', $manual->invoice_number) }}"
                               class="w-full text-sm rounded-md border-gray-300">
                        @error('invoice_number') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Invitados</label>
                        <input type="text" name="guests" placeholder="NA"
                               value="{{ old('guests', $manual->guests) }}"
                               class="w-full text-sm rounded-md border-gray-300">
                        @error('guests') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Observaciones</label>
                    <textarea name="observations" rows="5"
                              placeholder="Ej: La capacitación se desarrolló de manera satisfactoria..."
                              class="w-full text-sm rounded-md border-gray-300">{{ old('observations', $manual->observations) }}</textarea>
                    @error('observations') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Sugerencias</label>
                    <textarea name="suggestions" rows="5"
                              placeholder="Ej: 1. Considerar material de apoyo para consulta posterior..."
                              class="w-full text-sm rounded-md border-gray-300">{{ old('suggestions', $manual->suggestions) }}</textarea>
                    @error('suggestions') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50">
                        Guardar datos
                    </button>
                </div>
            </form>
        </div>

    </div>

</x-ui.section>
