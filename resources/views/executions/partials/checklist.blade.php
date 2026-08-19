@php
    $locked = $execution->isFinalized();
    $grouped = $checklistItems->groupBy('section');
@endphp

@if (session('status'))
    <div class="mb-4 p-3 rounded bg-green-50 text-green-700">
        {{ session('status') }}
    </div>
@endif

<div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
    <div class="p-6">

        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-gray-900">Check-List de Sala de Clases</h3>
            <a href="#" onclick="window.print(); return false;"
               class="inline-flex items-center px-3 py-1.5 rounded-md border border-gray-300 text-xs font-medium text-gray-700 hover:bg-gray-50">
                🖨️ Imprimir
            </a>
        </div>

        <form method="POST" action="{{ route('executions.checklist.update', $execution) }}">
            @csrf
            @method('PUT')

            <fieldset @disabled($locked)>

                @foreach($grouped as $section => $itemsInSection)

                    <div class="mb-6">
                        <p class="text-sm font-bold text-gray-800 bg-gray-100 px-3 py-2 rounded-t-md">
                            {{ $section }}
                        </p>

                        @foreach($itemsInSection->groupBy('subsection') as $subsection => $itemsInSubsection)

                            <div class="border border-t-0 border-gray-100">
                                <p class="text-xs font-semibold text-gray-600 px-3 py-2 bg-gray-50">
                                    {{ $subsection }}
                                </p>

                                @foreach($itemsInSubsection as $item)
                                    @php
                                        $current = $checklistResponses[$item->id]->status ?? null;
                                    @endphp
                                    <div class="flex items-center justify-between gap-4 px-3 py-2 border-t border-gray-50 text-sm">
                                        <span class="text-gray-800">{{ $item->label }}</span>
                                        <div class="flex gap-4 shrink-0">
                                            @foreach(['realizado' => 'Realizado', 'no_realizado' => 'No Realizado', 'no_aplica' => 'No Aplica'] as $value => $label)
                                                <label class="flex items-center gap-1 text-xs text-gray-600">
                                                    <input type="radio" name="status[{{ $item->id }}]" value="{{ $value }}"
                                                           @checked($current === $value) @disabled($locked)>
                                                    {{ $label }}
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                        @endforeach
                    </div>

                @endforeach

            </fieldset>

            @unless($locked)
                <div class="flex justify-end pt-2">
                    <button class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md text-xs font-semibold uppercase tracking-widest hover:bg-indigo-500">
                        Grabar Check-List
                    </button>
                </div>
            @endunless

        </form>

    </div>
</div>
