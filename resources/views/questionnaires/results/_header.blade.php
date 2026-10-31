{{-- Subnavegación, filtro de fechas y acciones del panel de resultados. --}}
<div class="mb-6 space-y-4">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <nav class="inline-flex rounded-md border border-gray-200 bg-white p-1 text-sm">
            <a href="{{ route('questionnaires.results.summary', [$questionnaire] + $filters) }}"
               class="rounded px-3 py-1.5 {{ $view === 'summary' ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-600 hover:text-gray-900' }}">Resumen</a>
            <a href="{{ route('questionnaires.results.responses', [$questionnaire] + $filters) }}"
               class="rounded px-3 py-1.5 {{ $view === 'responses' ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-600 hover:text-gray-900' }}">Respuestas individuales</a>
        </nav>

        <div class="flex items-center gap-3">
            <a href="{{ route('questionnaires.results.export', [$questionnaire] + $filters) }}"
               class="px-4 py-2 rounded bg-indigo-600 text-white text-sm hover:bg-indigo-500">Exportar CSV</a>

            <form method="POST" action="{{ route('questionnaires.results.destroy-all', $questionnaire) }}"
                  onsubmit="return confirm('¿Eliminar TODAS las respuestas de esta encuesta? No se puede deshacer. El link y el QR no cambian.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-sm text-red-600 hover:underline">Borrar todas</button>
            </form>
        </div>
    </div>

    <form method="GET" class="flex flex-wrap items-end gap-3 text-sm">
        <div>
            <label class="block text-xs text-gray-500">Desde</label>
            <input type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="rounded-md border-gray-300 text-sm">
        </div>
        <div>
            <label class="block text-xs text-gray-500">Hasta</label>
            <input type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="rounded-md border-gray-300 text-sm">
        </div>
        <button type="submit" class="px-3 py-2 rounded border border-gray-300 text-gray-700 hover:bg-gray-50">Filtrar</button>
        @if ($filters)
            <a href="{{ url()->current() }}" class="py-2 text-gray-600 underline">Quitar filtro</a>
        @endif
        @error('to') <p class="w-full text-red-600">{{ $message }}</p> @enderror
        @error('from') <p class="w-full text-red-600">{{ $message }}</p> @enderror
    </form>
</div>
