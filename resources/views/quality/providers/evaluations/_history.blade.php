{{-- Historial de evaluaciones del proveedor (NCH 2728 — 7.4) --}}
@php($latest = $provider->evaluations->first())

<div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between gap-4">
        <div>
            <h3 class="font-semibold text-gray-800">Evaluaciones</h3>
            <p class="text-xs text-gray-500">NCH 2728 — 7.4. Selección y reevaluación del proveedor.</p>
        </div>

        <a href="{{ route('quality.providers.evaluations.create', $provider) }}"
           class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md text-xs font-semibold uppercase tracking-widest hover:bg-indigo-500 shrink-0">
            Evaluar proveedor
        </a>
    </div>

    @if($latest)
        <div class="px-6 py-4 border-b border-gray-100 flex flex-wrap items-center gap-3 text-sm">
            <span class="text-gray-600">Estado actual:</span>
            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $latest->resultBadge() }}">{{ $latest->resultLabel() }}</span>
            <span class="font-semibold text-gray-800">{{ $latest->scoreLabel() }}</span>

            @if($latest->isReevaluationOverdue())
                <span class="px-2 py-1 rounded-full text-xs font-medium bg-red-50 text-red-700">
                    Reevaluación vencida desde el {{ $latest->next_evaluation_at->format('d-m-Y') }}
                </span>
            @elseif($latest->next_evaluation_at)
                <span class="text-xs text-gray-500">Próxima reevaluación: {{ $latest->next_evaluation_at->format('d-m-Y') }}</span>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left border-b bg-gray-50">
                        <th class="py-2 px-3">Fecha</th>
                        <th class="py-2 px-3">Tipo</th>
                        <th class="py-2 px-3">Evaluador</th>
                        @foreach(\App\Models\ProviderEvaluation::CRITERIA as $criterion)
                            <th class="py-2 px-2 text-center text-xs font-medium" title="{{ $criterion['label'] }}">
                                {{ \Illuminate\Support\Str::limit($criterion['label'], 12) }}
                            </th>
                        @endforeach
                        <th class="py-2 px-3">Puntaje</th>
                        <th class="py-2 px-3">Resultado</th>
                        <th class="py-2 px-3"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($provider->evaluations as $evaluation)
                        <tr class="border-b align-top">
                            <td class="py-2 px-3 whitespace-nowrap">{{ $evaluation->evaluated_at->format('d-m-Y') }}</td>
                            <td class="py-2 px-3 text-gray-600">{{ $evaluation->type === 'selection' ? 'Selección' : 'Reevaluación' }}</td>
                            <td class="py-2 px-3 text-gray-600">{{ $evaluation->evaluator?->name ?? '—' }}</td>
                            @foreach(array_keys(\App\Models\ProviderEvaluation::CRITERIA) as $key)
                                <td class="py-2 px-2 text-center text-gray-700">{{ $evaluation->scoreFor($key) ?? '—' }}</td>
                            @endforeach
                            <td class="py-2 px-3 font-semibold text-gray-800 whitespace-nowrap">{{ $evaluation->scoreLabel() }}</td>
                            <td class="py-2 px-3">
                                <span class="px-2 py-1 rounded-full text-xs font-medium whitespace-nowrap {{ $evaluation->resultBadge() }}">{{ $evaluation->resultLabel() }}</span>
                            </td>
                            <td class="py-2 px-3 text-right">
                                <a href="{{ route('quality.providers.evaluations.edit', [$provider, $evaluation]) }}" class="text-xs text-gray-600 hover:underline">Editar</a>
                            </td>
                        </tr>
                        @if($evaluation->observations)
                            <tr class="border-b">
                                <td colspan="{{ 6 + count(\App\Models\ProviderEvaluation::CRITERIA) }}" class="px-3 pb-3 text-xs text-gray-600 whitespace-pre-line">{{ $evaluation->observations }}</td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="p-6 text-sm text-gray-500">
            Este proveedor aún no tiene evaluaciones. La norma exige evaluarlo antes de seleccionarlo y reevaluarlo periódicamente.
        </div>
    @endif
</div>
