<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $questionnaire->name }}</h2>
            <a href="{{ route('questionnaires.index') }}" class="underline text-sm text-gray-700 hover:text-gray-900">Volver</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <x-breadcrumb :items="$breadcrumbs" />

            @include('questionnaires._tabs', ['current' => 'results'])
            @include('questionnaires.results._header')

            <x-ui.card>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-left text-xs uppercase tracking-wider text-gray-500">
                            <tr>
                                <th class="px-4 py-3">N°</th>
                                <th class="px-4 py-3">Fecha</th>
                                <th class="px-4 py-3">Quién respondió</th>
                                <th class="px-4 py-3">Respuestas</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($responses as $response)
                                <tr>
                                    <td class="px-4 py-3 text-gray-500">{{ $response->id }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-gray-700">{{ $response->submittedAtLocal() }}</td>
                                    <td class="px-4 py-3">
                                        @if ($questionnaire->isAnonymous())
                                            <span class="text-gray-500">Anónima</span>
                                        @else
                                            <span class="text-gray-900">{{ $response->respondentName() ?? '—' }}</span>
                                            <div class="text-xs text-gray-500">{{ collect([$response->email, $response->organization])->filter()->implode(' · ') }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-gray-700">{{ $response->answers_count }}</td>
                                    <td class="px-4 py-3 text-right whitespace-nowrap">
                                        <a href="{{ route('questionnaires.results.show', [$questionnaire, $response]) }}" class="text-indigo-600 hover:underline mr-3">Ver</a>
                                        <form method="POST" action="{{ route('questionnaires.results.destroy', [$questionnaire, $response]) }}" class="inline"
                                              onsubmit="return confirm('¿Eliminar esta respuesta? No se puede deshacer.')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-red-600 hover:underline">Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                        Aún no hay respuestas{{ $filters ? ' en este período' : '' }}.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-ui.card>

            <div class="mt-4">{{ $responses->links() }}</div>
        </div>
    </div>
</x-app-layout>
