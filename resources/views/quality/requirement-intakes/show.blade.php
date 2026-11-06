@php
    $row = fn ($label, $value) => ['label' => $label, 'value' => filled($value) ? $value : '—'];

    $requirement = [
        $row('Empresa', $intake->company?->name ?? 'Empresa eliminada'),
        $row('Fecha de solicitud', $intake->requested_at->format('d-m-Y')),
        $row('Contacto', $intake->contact_name),
        $row('Correo', $intake->contact_email),
        $row('Teléfono', $intake->contact_phone),
        $row('N° de participantes', $intake->participants_estimate),
        $row('Modalidad', $intake->modalityLabel()),
        $row('Fecha deseada de inicio', optional($intake->desired_start_date)->format('d-m-Y')),
        $row('Franquicia SENCE', $intake->requires_sence ? 'Sí' : 'No'),
    ];

    $review = [
        $row('Revisado por', $intake->reviewer?->name),
        $row('Fecha de revisión', optional($intake->reviewed_at)->format('d-m-Y')),
    ];
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $intake->code }} — {{ $intake->title }}
            </h2>

            <div class="flex items-center gap-3 shrink-0">
                <span class="px-3 py-1 rounded-full text-xs font-medium {{ $intake->reviewStatusBadge() }}">{{ $intake->reviewStatusLabel() }}</span>

                <a href="{{ route('quality.requirement-intakes.edit', $intake) }}"
                   class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-md text-xs font-semibold uppercase tracking-widest hover:bg-gray-50">
                    Editar
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <x-breadcrumb :items="[
                ['label' => 'Ver Norma', 'url' => route('quality.norm.index')],
                ['label' => 'Levantamiento del Requerimiento', 'url' => route('quality.requirement-intakes.index')],
                ['label' => $intake->code],
            ]" />

            {{-- 7.2.1 --}}
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-800">Determinación del requisito</h3>
                    <p class="text-xs text-gray-500">NCH 2728 — 7.2.1</p>
                </div>

                <div class="p-6 space-y-6">
                    <dl class="grid grid-cols-1 sm:grid-cols-3 gap-x-6 gap-y-4 text-sm">
                        @foreach($requirement as $item)
                            <div>
                                <dt class="text-xs text-gray-500">{{ $item['label'] }}</dt>
                                <dd class="mt-0.5 text-gray-800">{{ $item['value'] }}</dd>
                            </div>
                        @endforeach
                    </dl>

                    <div>
                        <div class="text-xs text-gray-500">Detalle del requerimiento</div>
                        <div class="mt-1 text-sm text-gray-800 whitespace-pre-line">{{ $intake->details }}</div>
                    </div>

                    <div>
                        <div class="text-xs text-gray-500">Documento del cliente</div>
                        @if($hasAttachment)
                            <a href="{{ route('quality.requirement-intakes.attachment', $intake) }}" target="_blank"
                               class="mt-1 inline-block text-sm text-indigo-600 hover:underline">{{ $intake->attachment_name }}</a>
                        @elseif($intake->attachment_path)
                            <p class="mt-1 text-sm text-red-600">El archivo registrado no se encuentra en el servidor.</p>
                        @else
                            <p class="mt-1 text-sm text-gray-500">Sin documento adjunto.</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- 7.2.2 --}}
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-800">Revisión del requisito</h3>
                    <p class="text-xs text-gray-500">NCH 2728 — 7.2.2</p>
                </div>

                <div class="p-6 space-y-6">
                    @if($intake->isReviewed())
                        <dl class="grid grid-cols-1 sm:grid-cols-3 gap-x-6 gap-y-4 text-sm">
                            <div>
                                <dt class="text-xs text-gray-500">Resultado</dt>
                                <dd class="mt-0.5">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium {{ $intake->reviewStatusBadge() }}">{{ $intake->reviewStatusLabel() }}</span>
                                </dd>
                            </div>
                            @foreach($review as $item)
                                <div>
                                    <dt class="text-xs text-gray-500">{{ $item['label'] }}</dt>
                                    <dd class="mt-0.5 text-gray-800">{{ $item['value'] }}</dd>
                                </div>
                            @endforeach
                        </dl>

                        <div>
                            <div class="text-xs text-gray-500">Observaciones</div>
                            <div class="mt-1 text-sm text-gray-800 whitespace-pre-line">{{ $intake->review_notes ?: '—' }}</div>
                        </div>

                        <div>
                            <div class="text-xs text-gray-500">Presupuesto asociado</div>
                            @if($intake->budget)
                                <a href="{{ route('budgets.show', $intake->budget) }}" class="mt-1 inline-block text-sm text-indigo-600 hover:underline">
                                    #{{ $intake->budget->id }} — {{ $intake->budget->internal_name }}
                                </a>
                            @else
                                <p class="mt-1 text-sm text-gray-500">Sin presupuesto asociado.</p>
                            @endif
                        </div>
                    @else
                        <div class="rounded-md bg-amber-50 border border-amber-200 p-4 text-sm text-amber-800">
                            Este requerimiento aún no se revisa. Antes de comprometerse con el cliente, la OTEC debe confirmar que puede cumplirlo.
                            <a href="{{ route('quality.requirement-intakes.edit', $intake) }}" class="font-medium underline">Registrar revisión</a>
                        </div>
                    @endif
                </div>
            </div>

            <div class="flex items-center justify-between text-xs text-gray-500">
                <span>
                    Registrado {{ $intake->created_at->format('d-m-Y H:i') }}
                    @if($intake->creator) por {{ $intake->creator->name }} @endif
                </span>

                <form method="POST" action="{{ route('quality.requirement-intakes.destroy', $intake) }}"
                      onsubmit="return confirm('¿Eliminar el levantamiento {{ $intake->code }}? Esta acción no se puede deshacer.')">
                    @csrf
                    @method('DELETE')
                    <button class="text-red-600 hover:underline">Eliminar levantamiento</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
