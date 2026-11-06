@php
    $input = 'mt-1 w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500';

    $initialItems = old('items', $quoteRequest->items ?: []);
    $initialItems = array_values(array_map(fn ($item) => [
        'description' => $item['description'] ?? '',
        'quantity' => is_numeric($item['quantity'] ?? null)
            ? str_replace('.', ',', (string) (float) $item['quantity'])
            : (string) ($item['quantity'] ?? ''),
        'unit' => $item['unit'] ?? '',
    ], is_array($initialItems) ? $initialItems : []));

    if ($initialItems === []) {
        $initialItems[] = ['description' => '', 'quantity' => '', 'unit' => ''];
    }

    $amount = old('quoted_amount', $quoteRequest->quoted_amount);
@endphp

{{-- Solicitud --}}
<div class="bg-white shadow-sm sm:rounded-lg overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="font-semibold text-gray-800">Solicitud</h3>
        <p class="text-xs text-gray-500">Qué le pide la OTEC al proveedor. NCH 2728 — 7.4.</p>
    </div>

    <div class="p-6 space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium">Proveedor</label>
                <select name="provider_id" required class="{{ $input }}">
                    <option value="">Selecciona un proveedor</option>
                    @foreach($providers as $provider)
                        <option value="{{ $provider->id }}" @selected(old('provider_id', $quoteRequest->provider_id) == $provider->id)>
                            {{ $provider->name }}@if($provider->category) — {{ $provider->category }}@endif
                        </option>
                    @endforeach
                </select>
                @error('provider_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium">Fecha de solicitud</label>
                <input type="date" name="requested_at" required
                       value="{{ old('requested_at', optional($quoteRequest->requested_at)->format('Y-m-d')) }}" class="{{ $input }}">
                @error('requested_at') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium">Para la ejecución (opcional)</label>
                <select name="execution_id" class="{{ $input }}">
                    <option value="">Uso general de la OTEC</option>
                    @foreach($executions as $execution)
                        <option value="{{ $execution->id }}" @selected(old('execution_id', $quoteRequest->execution_id) == $execution->id)>
                            {{ $execution->internal_code }} — {{ $execution->course_name }}
                        </option>
                    @endforeach
                </select>
                @error('execution_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium">Responder antes del</label>
                <input type="date" name="needed_by"
                       value="{{ old('needed_by', optional($quoteRequest->needed_by)->format('Y-m-d')) }}" class="{{ $input }}">
                @error('needed_by') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Ítems --}}
        <div x-data="{ items: @js($initialItems) }">
            <label class="block text-sm font-medium">Ítems a cotizar</label>
            @error('items') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror

            <div class="mt-2 border border-gray-200 rounded-md overflow-hidden">
                <div class="hidden md:grid grid-cols-12 gap-2 px-3 py-2 bg-gray-50 text-xs text-gray-500">
                    <div class="col-span-7">Descripción</div>
                    <div class="col-span-2">Cantidad</div>
                    <div class="col-span-2">Unidad</div>
                    <div class="col-span-1"></div>
                </div>

                <template x-for="(item, index) in items" :key="index">
                    <div class="grid grid-cols-12 gap-2 px-3 py-2 border-t border-gray-100">
                        <input type="text" :name="`items[${index}][description]`" x-model="item.description"
                               placeholder="Ej: Arriendo de sala para 20 personas, 3 jornadas"
                               class="col-span-12 md:col-span-7 text-sm rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        <input type="text" inputmode="decimal" :name="`items[${index}][quantity]`" x-model="item.quantity"
                               placeholder="Cantidad"
                               class="col-span-5 md:col-span-2 text-sm rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        <input type="text" :name="`items[${index}][unit]`" x-model="item.unit"
                               placeholder="Ej: días, unidades"
                               class="col-span-5 md:col-span-2 text-sm rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        <button type="button" @click="items.splice(index, 1)" x-show="items.length > 1"
                                class="col-span-2 md:col-span-1 text-xs text-red-600 hover:underline" title="Quitar ítem">
                            Quitar
                        </button>
                    </div>
                </template>
            </div>

            <button type="button" @click="items.push({ description: '', quantity: '', unit: '' })"
                    class="mt-2 text-sm text-indigo-600 hover:underline">
                + Agregar ítem
            </button>

            @foreach($errors->get('items.*') as $messages)
                <p class="text-sm text-red-600 mt-1">{{ $messages[0] }}</p>
                @break
            @endforeach
        </div>

        <div>
            <label class="block text-sm font-medium">Condiciones (opcional)</label>
            <textarea name="conditions" rows="3" class="{{ $input }}"
                      placeholder="Lugar y fecha de entrega, forma de pago, requisitos (factura, certificaciones, seguros)...">{{ old('conditions', $quoteRequest->conditions) }}</textarea>
            <p class="text-xs text-gray-500 mt-1">Aparece en el PDF que se envía al proveedor.</p>
            @error('conditions') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
    </div>
</div>

{{-- Respuesta --}}
<div class="bg-white shadow-sm sm:rounded-lg overflow-hidden"
     x-data="{ status: @js(old('status', $quoteRequest->status ?? 'sent')) }">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="font-semibold text-gray-800">Respuesta del proveedor</h3>
        <p class="text-xs text-gray-500">Se completa cuando el proveedor envía su cotización.</p>
    </div>

    <div class="p-6 space-y-4">
        <div>
            <label class="block text-sm font-medium">Estado</label>
            <div class="mt-2 flex flex-wrap gap-4">
                @foreach($statuses as $value => $label)
                    <label class="inline-flex items-center gap-2 text-sm">
                        <input type="radio" name="status" value="{{ $value }}" x-model="status"
                               class="border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        {{ $label }}
                    </label>
                @endforeach
            </div>
            @error('status') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div x-show="status !== 'sent'" x-cloak class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium">Fecha de respuesta</label>
                    <input type="date" name="responded_at"
                           value="{{ old('responded_at', optional($quoteRequest->responded_at)->format('Y-m-d') ?? now()->format('Y-m-d')) }}"
                           class="{{ $input }}">
                    @error('responded_at') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium">Monto cotizado (CLP)</label>
                    <input type="text" inputmode="numeric" name="quoted_amount"
                           value="{{ filled($amount) ? number_format((int) preg_replace('/\D/', '', (string) $amount), 0, ',', '.') : '' }}"
                           placeholder="Ej: 450.000" class="{{ $input }}">
                    @error('quoted_amount') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium">Cotización del proveedor (archivo)</label>
                @if($quoteRequest->attachment_path)
                    <div class="mt-1 mb-2 flex items-center gap-3 text-sm">
                        <a href="{{ route('quality.quote-requests.attachment', $quoteRequest) }}" target="_blank" class="text-indigo-600 hover:underline">{{ $quoteRequest->attachment_name }}</a>
                        <label class="inline-flex items-center gap-1 text-gray-600">
                            <input type="checkbox" name="remove_attachment" value="1" class="rounded border-gray-300">
                            Quitar
                        </label>
                    </div>
                    <p class="text-xs text-gray-500 mb-1">Si subes otro archivo, reemplaza al actual.</p>
                @endif
                <input type="file" name="attachment" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png" class="block w-full text-sm mt-1">
                <p class="text-xs text-gray-500 mt-1">PDF, Word, Excel o imagen. Máximo 20 MB.</p>
                @error('attachment') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium">Observaciones</label>
                <textarea name="response_notes" rows="3" class="{{ $input }}"
                          placeholder="Por qué se aceptó o rechazó, comparación con otras cotizaciones, condiciones acordadas...">{{ old('response_notes', $quoteRequest->response_notes) }}</textarea>
                @error('response_notes') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>
</div>
