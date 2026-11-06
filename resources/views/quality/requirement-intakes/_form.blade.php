@php
    $input = 'mt-1 w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500';
    $budgetsByCompany = $budgets->groupBy(fn ($b) => $b->company?->name ?? 'Sin empresa');
@endphp

{{-- 7.2.1 Determinación del requisito --}}
<div class="bg-white shadow-sm sm:rounded-lg overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="font-semibold text-gray-800">Determinación del requisito</h3>
        <p class="text-xs text-gray-500">NCH 2728 — 7.2.1. Qué pide el cliente.</p>
    </div>

    <div class="p-6 space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium">Empresa</label>
                <select name="company_id" required class="{{ $input }}">
                    <option value="">Selecciona una empresa</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}" @selected(old('company_id', $intake->company_id) == $company->id)>{{ $company->name }}</option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-1">
                    ¿No está en la lista? <a href="{{ route('companies.create') }}" target="_blank" class="text-indigo-600 hover:underline">Créala en Empresas</a> y recarga esta página.
                </p>
                @error('company_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium">Fecha de solicitud</label>
                <input type="date" name="requested_at" required
                       value="{{ old('requested_at', optional($intake->requested_at)->format('Y-m-d')) }}"
                       class="{{ $input }}">
                @error('requested_at') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium">Contacto que solicita</label>
                <input type="text" name="contact_name" value="{{ old('contact_name', $intake->contact_name) }}" class="{{ $input }}">
                @error('contact_name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Correo</label>
                <input type="email" name="contact_email" value="{{ old('contact_email', $intake->contact_email) }}" class="{{ $input }}">
                @error('contact_email') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Teléfono</label>
                <input type="text" name="contact_phone" value="{{ old('contact_phone', $intake->contact_phone) }}" class="{{ $input }}">
                @error('contact_phone') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium">Título de la solicitud</label>
            <input type="text" name="title" required value="{{ old('title', $intake->title) }}"
                   placeholder="Ej: Curso de liderazgo para supervisores de bodega" class="{{ $input }}">
            @error('title') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium">Detalle del requerimiento</label>
            <textarea name="details" rows="4" required class="{{ $input }}"
                      placeholder="Necesidad, objetivos, perfil de los participantes, horarios, lugar, requisitos especiales...">{{ old('details', $intake->details) }}</textarea>
            @error('details') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium">N° de participantes</label>
                <input type="number" min="1" name="participants_estimate"
                       value="{{ old('participants_estimate', $intake->participants_estimate) }}" class="{{ $input }}">
                @error('participants_estimate') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Modalidad</label>
                <select name="modality" class="{{ $input }}">
                    <option value="">Sin definir</option>
                    @foreach($modalities as $value => $label)
                        <option value="{{ $value }}" @selected(old('modality', $intake->modality) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('modality') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Fecha deseada de inicio</label>
                <input type="date" name="desired_start_date"
                       value="{{ old('desired_start_date', optional($intake->desired_start_date)->format('Y-m-d')) }}" class="{{ $input }}">
                @error('desired_start_date') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="flex items-end pb-2">
                <label class="inline-flex items-center gap-2 text-sm">
                    <input type="hidden" name="requires_sence" value="0">
                    <input type="checkbox" name="requires_sence" value="1"
                           @checked(old('requires_sence', $intake->requires_sence))
                           class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    Requiere franquicia SENCE
                </label>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium">Programa o documento del cliente (opcional)</label>
            @if($intake->attachment_path)
                <div class="mt-1 mb-2 flex items-center gap-3 text-sm">
                    <a href="{{ route('quality.requirement-intakes.attachment', $intake) }}" target="_blank" class="text-indigo-600 hover:underline">{{ $intake->attachment_name }}</a>
                    <label class="inline-flex items-center gap-1 text-gray-600">
                        <input type="checkbox" name="remove_attachment" value="1" class="rounded border-gray-300">
                        Quitar
                    </label>
                </div>
                <p class="text-xs text-gray-500 mb-1">Si subes otro archivo, reemplaza al actual.</p>
            @endif
            <input type="file" name="attachment" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png" class="block w-full text-sm mt-1">
            <p class="text-xs text-gray-500 mt-1">PDF, Word, Excel, PowerPoint o imagen. Máximo 20 MB.</p>
            @error('attachment') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
    </div>
</div>

{{-- 7.2.2 Revisión del requisito --}}
<div class="bg-white shadow-sm sm:rounded-lg overflow-hidden"
     x-data="{ status: @js(old('review_status', $intake->review_status ?? 'pending')) }">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="font-semibold text-gray-800">Revisión del requisito</h3>
        <p class="text-xs text-gray-500">NCH 2728 — 7.2.2. Confirmar que la OTEC puede cumplir antes de comprometerse.</p>
    </div>

    <div class="p-6 space-y-4">
        <div>
            <label class="block text-sm font-medium">Resultado</label>
            <div class="mt-2 flex flex-wrap gap-4">
                @foreach($reviewStatuses as $value => $label)
                    <label class="inline-flex items-center gap-2 text-sm">
                        <input type="radio" name="review_status" value="{{ $value }}" x-model="status"
                               class="border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        {{ $label }}
                    </label>
                @endforeach
            </div>
            @error('review_status') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div x-show="status !== 'pending'" x-cloak class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium">Revisado por</label>
                    <select name="reviewed_by" class="{{ $input }}">
                        <option value="">Selecciona</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" @selected(old('reviewed_by', $intake->reviewed_by ?? auth()->id()) == $user->id)>{{ $user->name }}</option>
                        @endforeach
                    </select>
                    @error('reviewed_by') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium">Fecha de revisión</label>
                    <input type="date" name="reviewed_at"
                           value="{{ old('reviewed_at', optional($intake->reviewed_at)->format('Y-m-d') ?? now()->format('Y-m-d')) }}"
                           class="{{ $input }}">
                    @error('reviewed_at') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium">Observaciones de la revisión</label>
                <textarea name="review_notes" rows="3" class="{{ $input }}"
                          placeholder="Capacidad, relatores disponibles, plazos, infraestructura, requisitos SENCE...">{{ old('review_notes', $intake->review_notes) }}</textarea>
                <p class="text-xs text-gray-500 mt-1" x-show="status === 'not_feasible'">Obligatorio cuando no se puede cumplir.</p>
                @error('review_notes') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium">Presupuesto asociado (opcional)</label>
                <select name="budget_id" class="{{ $input }}">
                    <option value="">Sin presupuesto</option>
                    @foreach($budgetsByCompany as $companyName => $companyBudgets)
                        <optgroup label="{{ $companyName }}">
                            @foreach($companyBudgets as $budget)
                                <option value="{{ $budget->id }}" @selected(old('budget_id', $intake->budget_id) == $budget->id)>
                                    #{{ $budget->id }} — {{ $budget->internal_name }}
                                    @if($budget->budget_date) ({{ \Illuminate\Support\Carbon::parse($budget->budget_date)->format('d-m-Y') }}) @endif
                                </option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-1">Debe ser de la misma empresa del requerimiento.</p>
                @error('budget_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>
</div>
