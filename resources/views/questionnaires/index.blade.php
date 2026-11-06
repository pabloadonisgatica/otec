<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Encuestas</h2>

            <a href="{{ route('questionnaires.create') }}"
               class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md text-xs font-semibold uppercase tracking-widest hover:bg-indigo-500">
                Nueva encuesta
            </a>
        </div>
    </x-slot>

    @php
        $badges = [
            'active' => 'bg-green-100 text-green-800',
            'inactive' => 'bg-gray-100 text-gray-700',
            'closed' => 'bg-amber-100 text-amber-800',
        ];
    @endphp

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <x-breadcrumb :items="$breadcrumbs" />

            <x-ui.card>
                <div class="p-6">
                    <form method="GET" action="{{ route('questionnaires.index') }}" class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-3">
                        <input type="text" name="q" value="{{ $search }}" placeholder="Buscar por nombre…"
                               class="md:col-span-2 rounded-md border-gray-300">

                        <select name="type" class="rounded-md border-gray-300" onchange="this.form.submit()">
                            <option value="">Todos los tipos</option>
                            @foreach ($types as $option)
                                <option value="{{ $option->value }}" @selected($type === $option->value)>{{ $option->label() }}</option>
                            @endforeach
                        </select>

                        <select name="status" class="rounded-md border-gray-300" onchange="this.form.submit()">
                            <option value="">Todos los estados</option>
                            @foreach ($statuses as $key => $label)
                                <option value="{{ $key }}" @selected($status === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </form>

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-left border-b border-gray-200 text-gray-700">
                                    <th class="py-2 pr-4">Encuesta</th>
                                    <th class="py-2 pr-4">Asociada a</th>
                                    <th class="py-2 pr-4">Respuestas</th>
                                    <th class="py-2 pr-4">Estado</th>
                                    <th class="py-2 pr-4">Cierre</th>
                                    <th class="py-2 pr-1 text-right"><span class="sr-only">Acciones</span></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($questionnaires as $questionnaire)
                                    <tr class="border-b border-gray-100 align-top">
                                        <td class="py-3 pr-4">
                                            <a href="{{ route('questionnaires.edit', $questionnaire) }}" class="font-medium text-gray-900 hover:text-indigo-700">
                                                {{ $questionnaire->name }}
                                            </a>
                                            <div class="text-xs text-gray-500">
                                                {{ $questionnaire->type->label() }}
                                                @if ($questionnaire->hasBuilder())
                                                    · {{ $questionnaire->questions_count }} {{ $questionnaire->questions_count === 1 ? 'pregunta' : 'preguntas' }}
                                                @endif
                                            </div>
                                        </td>
                                        <td class="py-3 pr-4 text-gray-600">{{ $questionnaire->associationLabel() ?? '—' }}</td>
                                        <td class="py-3 pr-4 whitespace-nowrap">
                                            <a href="{{ route('questionnaires.results.summary', $questionnaire) }}" class="font-medium text-indigo-700 hover:underline">{{ $questionnaire->responses_count }}</a>
                                            @if ($questionnaire->responses_max_created_at)
                                                <div class="text-xs text-gray-500">
                                                    última {{ \Illuminate\Support\Carbon::parse($questionnaire->responses_max_created_at, 'UTC')->timezone(\App\Models\Questionnaire::TIMEZONE)->format('d-m H:i') }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="py-3 pr-4">
                                            <span class="px-2 py-1 rounded text-xs {{ $badges[$questionnaire->status()] }}">
                                                {{ $questionnaire->statusLabel() }}
                                            </span>
                                        </td>
                                        <td class="py-3 pr-4 text-gray-600 whitespace-nowrap">{{ $questionnaire->closesAtLocal() ?? 'Sin cierre' }}</td>
                                        <td class="py-2 pr-1">
                                            <x-ui.row-actions label="Más acciones de «{{ $questionnaire->name }}»">
                                                <x-ui.row-actions.link :href="route('questionnaires.share', $questionnaire)">Compartir</x-ui.row-actions.link>

                                                <x-slot:menu>
                                                    <x-ui.row-actions.item :href="route('questionnaires.edit', $questionnaire)">Editar</x-ui.row-actions.item>
                                                    @if ($questionnaire->hasBuilder())
                                                        <x-ui.row-actions.item :href="route('questionnaires.builder.edit', $questionnaire)">Preguntas</x-ui.row-actions.item>
                                                    @endif
                                                    <x-ui.row-actions.item :href="route('questionnaires.results.summary', $questionnaire)">Ver resultados</x-ui.row-actions.item>
                                                    <x-ui.row-actions.item :action="route('questionnaires.duplicate', $questionnaire)">Duplicar</x-ui.row-actions.item>
                                                    <x-ui.row-actions.divider />
                                                    <x-ui.row-actions.item :action="route('questionnaires.destroy', $questionnaire)" method="DELETE" danger
                                                                           confirm="¿Eliminar esta encuesta? Su link dejará de funcionar.">Eliminar</x-ui.row-actions.item>
                                                </x-slot:menu>
                                            </x-ui.row-actions>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-8 text-center text-gray-500">
                                            @if ($search || $status || $type)
                                                No hay encuestas con esos filtros.
                                            @else
                                                Aún no hay encuestas. Crea la primera con «Nueva encuesta».
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </x-ui.card>

            <div class="mt-4">
                {{ $questionnaires->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
