@php
    $c = $communication ?? null;
    $selectedTopics = old('topics', $c->topics ?? []);
@endphp

<div>
    <label class="block text-sm font-medium mb-2">¿Qué se comunicó?</label>
    <div class="space-y-2">
        @foreach(\App\Models\QualityInternalCommunication::TOPICS as $key => $label)
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="topics[]" value="{{ $key }}"
                       @checked(in_array($key, $selectedTopics))>
                {{ $label }}
            </label>
        @endforeach
    </div>
    @error('topics') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium">Canal</label>
        <select name="channel" required class="mt-1 w-full rounded-md border-gray-300 text-sm">
            <option value="">Selecciona un canal</option>
            @foreach(\App\Models\QualityInternalCommunication::CHANNELS as $channel)
                <option value="{{ $channel }}" @selected(old('channel', $c->channel ?? '') === $channel)>{{ $channel }}</option>
            @endforeach
        </select>
        @error('channel') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm font-medium">Fecha</label>
        <input type="date" name="communicated_at"
               value="{{ old('communicated_at', optional($c->communicated_at ?? null)->format('Y-m-d')) }}" required
               class="mt-1 w-full rounded-md border-gray-300 text-sm">
        @error('communicated_at') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>
</div>

<div>
    <label class="block text-sm font-medium">A quién (audiencia)</label>
    <input type="text" name="audience" value="{{ old('audience', $c->audience ?? '') }}" required
           placeholder="Ej: Todo el personal, Relatores turno mañana, Personal administrativo"
           class="mt-1 w-full rounded-md border-gray-300 text-sm">
    @error('audience') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
</div>

<div>
    <label class="block text-sm font-medium">Notas (opcional)</label>
    <textarea name="notes" rows="3" class="mt-1 w-full rounded-md border-gray-300 text-sm">{{ old('notes', $c->notes ?? '') }}</textarea>
</div>
