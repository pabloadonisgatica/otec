<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Editar presupuesto #{{ $budget->id }}
            </h2>

            <div class="flex items-center gap-3">
                <a href="{{ route('budgets.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600">
                    Volver
                </a>

                <a href="{{ route('budgets.pdf', $budget) }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-200 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200">
                    PDF
                </a>
            </div>
        </div>
    </x-slot>

@php
    $sheetInputs = $budget->sheet?->inputs ?? [];

    $costLines = $sheetInputs['cost_lines'] ?? [
        ['category' => 'honorarios', 'name' => 'Relator', 'qty' => 1, 'unit_cost' => 0, 'required' => true],
        ['category' => 'materiales', 'name' => 'Diplomas', 'qty' => 1, 'unit_cost' => 0, 'required' => true],
    ];

    $applyMargins = $sheetInputs['apply_margins'] ?? false;

    $defaultBlocks = [
        'service' => true,
        'courses' => true,
        'costs' => true,
        'margins' => true,
        'texts' => true,
        'approval' => true,
    ];

    $blocks = $sheetInputs['ui_blocks'] ?? $defaultBlocks;

    // Si viene guardado como string JSON, lo convertimos a array
    if (is_string($blocks)) {
        $decoded = json_decode($blocks, true);
        $blocks = is_array($decoded) ? $decoded : $defaultBlocks;
    }

    $blocks = array_merge($defaultBlocks, $blocks);

    $alpinePayload = [
        'costLines' => $costLines,
        'applyMargins' => (bool) $applyMargins,
        'blocks' => $blocks,
    ];
@endphp


    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <x-breadcrumb :items="$breadcrumbs" />

            @if (session('success'))
                <div class="mb-4 p-3 rounded bg-green-50 text-green-700">
                    {{ session('success') }}
                </div>
            @endif

<form method="POST" action="{{ route('budgets.update', $budget) }}" x-data="budgetForm(@js($alpinePayload))" x-cloak>


                @csrf
                @method('PUT')

                <div class="grid grid-cols-12 gap-6">

                    {{-- COLUMNA PRINCIPAL --}}
                    <div class="col-span-12 lg:col-span-8 space-y-6">

                        {{-- BLOQUE: Datos base --}}
                        <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                            <div class="p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="font-semibold text-gray-800">Datos base</h3>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium">Nombre interno</label>
                                        <input name="internal_name"
                                               value="{{ old('internal_name', $budget->internal_name) }}"
                                               class="mt-1 w-full rounded border-gray-300"
                                               required>
                                        @error('internal_name') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium">Empresa</label>
                                        <select name="company_id" class="mt-1 w-full rounded border-gray-300" required>
                                            @foreach (\App\Models\Company::orderBy('name')->get() as $company)
                                                <option value="{{ $company->id }}" @selected(old('company_id', $budget->company_id) == $company->id)>
                                                    {{ $company->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium">Estado</label>
                                        <select name="status" class="mt-1 w-full rounded border-gray-300" required>
                                            @foreach (['draft'=>'Borrador','pending'=>'Pendiente','approved'=>'Aceptado','rejected'=>'Rechazado'] as $k=>$label)
                                                <option value="{{ $k }}" @selected(old('status', $budget->status) === $k)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium">Código curso (opcional)</label>
                                        <input name="course_code"
                                               value="{{ old('course_code', $budget->course_code) }}"
                                               class="mt-1 w-full rounded border-gray-300">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium">Tipo de presupuesto</label>
                                        <select name="sheet[type]" class="mt-1 w-full rounded border-gray-300">
                                            @php $type = $sheetInputs['type'] ?? 'mixto'; @endphp
                                            <option value="sence" @selected($type==='sence')>SENCE</option>
                                            <option value="no_sence" @selected($type==='no_sence')>No SENCE</option>
                                            <option value="mixto" @selected($type==='mixto')>Mixto / Especial</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- BLOQUE: Identificación del servicio (colapsable) --}}
                        <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                            <div class="p-6">
                                <button type="button" class="w-full flex items-center justify-between"
                                        @click="toggle('service')">
                                    <h3 class="font-semibold text-gray-800">Identificación del servicio</h3>
                                    <span class="text-sm text-gray-500" x-text="blocks.service ? 'Ocultar' : 'Mostrar'"></span>
                                </button>

                                <div class="mt-4" x-show="blocks.service" x-transition>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="md:col-span-2">
                                            <label class="block text-sm font-medium">Nombre del programa / propuesta</label>
                                            <input name="sheet[service_name]"
                                                   value="{{ old('sheet.service_name', $sheetInputs['service_name'] ?? '') }}"
                                                   class="mt-1 w-full rounded border-gray-300">
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium">Modalidad</label>
                                            <select name="sheet[modality]" class="mt-1 w-full rounded border-gray-300">
                                                @php $mod = $sheetInputs['modality'] ?? 'presencial'; @endphp
                                                <option value="presencial" @selected($mod==='presencial')>Presencial</option>
                                                <option value="online" @selected($mod==='online')>Online</option>
                                                <option value="mixta" @selected($mod==='mixta')>Mixta</option>
                                            </select>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium">Lugar</label>
                                            <input name="sheet[location]"
                                                   value="{{ old('sheet.location', $sheetInputs['location'] ?? '') }}"
                                                   class="mt-1 w-full rounded border-gray-300"
                                                   placeholder="Ej: Empresa cliente / Sala OTEC / A definir">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- BLOQUE: Cursos (ya lo tienes, lo dejamos como bloque colapsable) --}}
                        <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                            <div class="p-6">
                                <button type="button" class="w-full flex items-center justify-between"
                                        @click="toggle('courses')">
                                    <h3 class="font-semibold text-gray-800">Cursos</h3>
                                    <span class="text-sm text-gray-500" x-text="blocks.courses ? 'Ocultar' : 'Mostrar'"></span>
                                </button>

                                <div class="mt-4" x-show="blocks.courses" x-transition>
                                    {{-- Aquí va tu tabla de cursos (la que ya implementamos) --}}
                                    @includeIf('budgets.partials.courses')
                                </div>
                            </div>
                        </div>

                        {{-- BLOQUE: Planilla de costos (traje a la medida) --}}
                        <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                            <div class="p-6">
                                <button type="button" class="w-full flex items-center justify-between"
                                        @click="toggle('costs')">
                                    <h3 class="font-semibold text-gray-800">Planilla de costos</h3>
                                    <span class="text-sm text-gray-500" x-text="blocks.costs ? 'Ocultar' : 'Mostrar'"></span>
                                </button>

                                <div class="mt-4" x-show="blocks.costs" x-transition>
                                    <div class="flex items-center justify-between mb-3">
                                        <p class="text-sm text-gray-600">
                                            Agrega o ajusta ítems según el caso. “Relator” y “Diplomas” son mínimos.
                                        </p>

                                        <button type="button"
                                                class="inline-flex items-center px-3 py-2 bg-gray-700 text-white rounded-md text-xs font-semibold uppercase hover:bg-gray-600"
                                                @click="addCost()">
                                            + Agregar ítem
                                        </button>
                                    </div>

                                    <div class="overflow-x-auto">
                                        <table class="min-w-full text-sm">
                                            <thead>
                                                <tr class="text-left border-b">
                                                    <th class="py-2">Categoría</th>
                                                    <th class="py-2">Ítem</th>
                                                    <th class="py-2 w-24 text-right">Cant.</th>
                                                    <th class="py-2 w-36 text-right">Valor unit.</th>
                                                    <th class="py-2 w-36 text-right">Total</th>
                                                    <th class="py-2 w-20 text-right">Acción</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                <template x-for="(c, idx) in costLines" :key="idx">
                                                    <tr class="border-b">
                                                        <td class="py-2 pr-2">
                                                            <input type="hidden" :name="`sheet[cost_lines][${idx}][required]`" :value="c.required ? 1 : 0">
                                                            <select class="w-full rounded border-gray-300"
                                                                    :name="`sheet[cost_lines][${idx}][category]`"
                                                                    x-model="c.category">
                                                                <option value="honorarios">Honorarios</option>
                                                                <option value="materiales">Materiales</option>
                                                                <option value="diplomas">Diplomas</option>
                                                                <option value="logistica">Logística</option>
                                                                <option value="desarrollo">Desarrollo</option>
                                                                <option value="otros">Otros</option>
                                                            </select>
                                                        </td>

                                                        <td class="py-2 pr-2">
                                                            <input class="w-full rounded border-gray-300"
                                                                   :name="`sheet[cost_lines][${idx}][name]`"
                                                                   x-model="c.name">
                                                        </td>

                                                        <td class="py-2 pr-2 text-right">
                                                            <input type="number" min="0"
                                                                   class="w-full rounded border-gray-300 text-right"
                                                                   :name="`sheet[cost_lines][${idx}][qty]`"
                                                                   x-model="c.qty">
                                                        </td>

                                                        <td class="py-2 pr-2 text-right">
                                                            <input type="number" min="0"
                                                                   class="w-full rounded border-gray-300 text-right"
                                                                   :name="`sheet[cost_lines][${idx}][unit_cost]`"
                                                                   x-model="c.unit_cost">
                                                        </td>

                                                        <td class="py-2 text-right font-medium">
                                                            $<span x-text="costTotal(c).toLocaleString('es-CL')"></span>
                                                        </td>

                                                        <td class="py-2 text-right whitespace-nowrap">
                                                            <button type="button"
                                                                    class="underline text-sm text-red-600 hover:text-red-800"
                                                                    :disabled="c.required"
                                                                    :class="c.required ? 'opacity-40 cursor-not-allowed' : ''"
                                                                    @click="removeCost(idx)">
                                                                Quitar
                                                            </button>
                                                        </td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="mt-3 text-right text-sm">
                                        <span class="text-gray-600">Total costos:</span>
                                        <strong>$<span x-text="sumCosts().toLocaleString('es-CL')"></span></strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- BLOQUE: Márgenes (activable) --}}
                        <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                            <div class="p-6">
                                <button type="button" class="w-full flex items-center justify-between"
                                        @click="toggle('margins')">
                                    <h3 class="font-semibold text-gray-800">Márgenes y ajustes</h3>
                                    <span class="text-sm text-gray-500" x-text="blocks.margins ? 'Ocultar' : 'Mostrar'"></span>
                                </button>

                                <div class="mt-4" x-show="blocks.margins" x-transition>
                                    <label class="inline-flex items-center gap-2 text-sm">
                                        <input type="checkbox" class="rounded border-gray-300"
                                               name="sheet[apply_margins]"
                                               value="1"
                                               x-model="applyMargins">
                                        Aplicar márgenes y comisiones
                                    </label>

                                    <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4" x-show="applyMargins" x-transition>
                                        <div>
                                            <label class="block text-sm font-medium">% Margen contribución</label>
                                            <input type="number" min="0" max="100"
                                                   class="mt-1 w-full rounded border-gray-300"
                                                   name="sheet[margen_contrib]"
                                                   value="{{ old('sheet.margen_contrib', $sheetInputs['margen_contrib'] ?? 0) }}">
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium">% Gestión venta</label>
                                            <input type="number" min="0" max="100"
                                                   class="mt-1 w-full rounded border-gray-300"
                                                   name="sheet[gestion_venta]"
                                                   value="{{ old('sheet.gestion_venta', $sheetInputs['gestion_venta'] ?? 0) }}">
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium">% Comisión venta</label>
                                            <input type="number" min="0" max="100"
                                                   class="mt-1 w-full rounded border-gray-300"
                                                   name="sheet[comision_venta]"
                                                   value="{{ old('sheet.comision_venta', $sheetInputs['comision_venta'] ?? 0) }}">
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium">% Comisión PH</label>
                                            <input type="number" min="0" max="100"
                                                   class="mt-1 w-full rounded border-gray-300"
                                                   name="sheet[comision_ph]"
                                                   value="{{ old('sheet.comision_ph', $sheetInputs['comision_ph'] ?? 0) }}">
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium">% Gestión sobre utilidad</label>
                                            <input type="number" min="0" max="100"
                                                   class="mt-1 w-full rounded border-gray-300"
                                                   name="sheet[gestion_sobre_util]"
                                                   value="{{ old('sheet.gestion_sobre_util', $sheetInputs['gestion_sobre_util'] ?? 0) }}">
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium">Ajuste manual (CLP)</label>
                                            <input type="number"
                                                   class="mt-1 w-full rounded border-gray-300"
                                                   name="sheet[manual_adjust]"
                                                   value="{{ old('sheet.manual_adjust', $sheetInputs['manual_adjust'] ?? 0) }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- BLOQUE: Textos comerciales --}}
                        <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                            <div class="p-6">
                                <button type="button" class="w-full flex items-center justify-between"
                                        @click="toggle('texts')">
                                    <h3 class="font-semibold text-gray-800">Textos comerciales</h3>
                                    <span class="text-sm text-gray-500" x-text="blocks.texts ? 'Ocultar' : 'Mostrar'"></span>
                                </button>

                                <div class="mt-4" x-show="blocks.texts" x-transition>
                                    <div class="grid grid-cols-1 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium">Observaciones</label>
                                            <textarea name="observations" class="mt-1 w-full rounded border-gray-300" rows="3">{{ old('observations', $budget->observations) }}</textarea>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium">Incluye</label>
                                            <textarea name="includes" class="mt-1 w-full rounded border-gray-300" rows="2">{{ old('includes', $budget->includes) }}</textarea>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium">No incluye</label>
                                            <textarea name="excludes" class="mt-1 w-full rounded border-gray-300" rows="2">{{ old('excludes', $budget->excludes) }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Guardar --}}
                        <div class="flex justify-end gap-3">
                            <a href="{{ route('budgets.index') }}"
                               class="px-4 py-2 rounded bg-gray-100 text-gray-800 hover:bg-gray-200">
                                Cancelar
                            </a>

                            <button class="px-4 py-2 rounded bg-indigo-600 text-white hover:bg-indigo-500">
                                Guardar
                            </button>
                        </div>
                    </div>

                    {{-- RESUMEN STICKY --}}
                    <aside class="col-span-12 lg:col-span-4">
                        <div class="sticky top-6 bg-white shadow-sm sm:rounded-lg overflow-hidden">
                            <div class="p-6">
                                <h3 class="font-semibold text-gray-800 mb-4">Resumen</h3>

                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span>Total cursos (guardado)</span>
                                        <strong>${{ number_format($budget->courses->sum('line_total'), 0, ',', '.') }}</strong>
                                    </div>

                                    <div class="flex justify-between">
                                        <span>Total costos (en pantalla)</span>
                                        <strong>$<span x-text="sumCosts().toLocaleString('es-CL')"></span></strong>
                                    </div>

                                    <div class="border-t pt-3 flex justify-between">
                                        <span>Total presupuesto (guardado)</span>
                                        <strong>${{ number_format($budget->total_amount ?? 0, 0, ',', '.') }}</strong>
                                    </div>

                                    <p class="text-xs text-gray-500 mt-2">
                                        * El total final se actualizará cuando implementemos la fórmula completa del Excel.
                                    </p>
                                </div>

                                {{-- Guardamos UI state --}}
                                <input type="hidden" name="sheet[ui_blocks]" :value="JSON.stringify(blocks)">
                            </div>
                        </div>
                    </aside>

                </div>
            </form>
        </div>
    </div>

<script>
    function budgetForm(payload) {
        payload = payload || {};

        const defaultBlocks = {
            service: true,
            courses: true,
            costs: true,
            margins: true,
            texts: true,
            approval: true
        };

        // blocks puede venir como string JSON o como objeto
        let blocks = payload.blocks;
        if (typeof blocks === 'string') {
            try { blocks = JSON.parse(blocks); } catch(e) { blocks = null; }
        }
        blocks = Object.assign({}, defaultBlocks, (blocks && typeof blocks === 'object') ? blocks : {});

        // costLines puede venir como string JSON o array
        let costLines = payload.costLines;
        if (typeof costLines === 'string') {
            try { costLines = JSON.parse(costLines); } catch(e) { costLines = null; }
        }
        costLines = Array.isArray(costLines) ? costLines : [];

        // asegurar mínimos obligatorios
        const hasRelator = costLines.some(l => l?.required === true && (String(l?.name || '').toLowerCase().includes('relator')));
        const hasDiplomas = costLines.some(l => l?.required === true && (String(l?.name || '').toLowerCase().includes('diploma')));

        if (!hasRelator) costLines.unshift({ category: 'honorarios', name: 'Relator', qty: 1, unit_cost: 0, required: true });
        if (!hasDiplomas) costLines.push({ category: 'materiales', name: 'Diplomas', qty: 1, unit_cost: 0, required: true });

        return {
            blocks,
            costLines,
            applyMargins: !!payload.applyMargins,

            toggle(key) {
                this.blocks[key] = !this.blocks[key];
            },

            addCost() {
                this.costLines.push({ category: 'otros', name: '', qty: 1, unit_cost: 0, required: false });
            },

            removeCost(idx) {
                const row = this.costLines[idx];
                if (row?.required) return;
                this.costLines.splice(idx, 1);
            },

            costTotal(c) {
                const qty = parseInt(c?.qty ?? 0, 10) || 0;
                const unit = parseInt(c?.unit_cost ?? 0, 10) || 0;
                return qty * unit;
            },

            sumCosts() {
                return this.costLines.reduce((acc, c) => acc + this.costTotal(c), 0);
            },
        };
    }
</script>


</x-app-layout>
