@php
    $v = fn($key) => old($key, $provider?->$key);
@endphp

<div>
    <label class="block text-sm font-medium">RUT</label>
    <input name="rut" value="{{ $v('rut') }}" class="mt-1 w-full rounded border-gray-300">
    @error('rut') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
</div>

<div>
    <label class="block text-sm font-medium">Nombre / Razón social</label>
    <input name="name" value="{{ $v('name') }}" class="mt-1 w-full rounded border-gray-300" required>
    @error('name') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium">Giro / Razón social (opcional)</label>
        <input name="business_name" value="{{ $v('business_name') }}" class="mt-1 w-full rounded border-gray-300">
    </div>
    <div>
        <label class="block text-sm font-medium">Categoría / Qué provee</label>
        <input name="category" value="{{ $v('category') }}" placeholder="Ej: Catering, Material didáctico, Infraestructura, Transporte"
               class="mt-1 w-full rounded border-gray-300">
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium">Email</label>
        <input name="email" type="email" value="{{ $v('email') }}" class="mt-1 w-full rounded border-gray-300">
        @error('email') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
    </div>
    <div>
        <label class="block text-sm font-medium">Teléfono</label>
        <input name="phone" value="{{ $v('phone') }}" class="mt-1 w-full rounded border-gray-300">
    </div>
</div>

<div>
    <label class="block text-sm font-medium">Dirección</label>
    <input name="address" value="{{ $v('address') }}" class="mt-1 w-full rounded border-gray-300">
</div>

@php
    $regions = config('chile.regions', []);
    $selectedRegion = old('region', $provider->region ?? '');
    $selectedCommune = old('commune', $provider->commune ?? '');
@endphp

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium">Región</label>
        <select name="region" id="region" class="mt-1 w-full rounded border-gray-300">
            <option value="">Selecciona región</option>
            @foreach(array_keys($regions) as $region)
                <option value="{{ $region }}" @selected($selectedRegion === $region)>{{ $region }}</option>
            @endforeach
        </select>
        @error('region') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium">Comuna</label>
        <select name="commune" id="commune" class="mt-1 w-full rounded border-gray-300">
            <option value="">Selecciona comuna</option>
        </select>
        @error('commune') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
    </div>
</div>

<hr class="my-4">

<p class="font-semibold">Contacto principal</p>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium">Nombre contacto</label>
        <input name="contact_name" value="{{ $v('contact_name') }}" class="mt-1 w-full rounded border-gray-300">
    </div>
    <div>
        <label class="block text-sm font-medium">Email contacto</label>
        <input name="contact_email" type="email" value="{{ $v('contact_email') }}" class="mt-1 w-full rounded border-gray-300">
    </div>
</div>

<div>
    <label class="block text-sm font-medium">Teléfono contacto</label>
    <input name="contact_phone" value="{{ $v('contact_phone') }}" class="mt-1 w-full rounded border-gray-300">
</div>

<div>
    <label class="block text-sm font-medium">Notas</label>
    <textarea name="notes" rows="3" class="mt-1 w-full rounded border-gray-300">{{ $v('notes') }}</textarea>
</div>

<script>
    const REGIONS = @json($regions);
    const regionSelect = document.getElementById('region');
    const communeSelect = document.getElementById('commune');
    const selectedCommune = @json($selectedCommune);

    function loadCommunes(region) {
        communeSelect.innerHTML = '<option value="">Selecciona comuna</option>';
        if (!region || !REGIONS[region]) return;

        REGIONS[region].forEach(c => {
            const opt = document.createElement('option');
            opt.value = c;
            opt.textContent = c;
            if (c === selectedCommune) opt.selected = true;
            communeSelect.appendChild(opt);
        });
    }

    loadCommunes(regionSelect.value);

    regionSelect.addEventListener('change', (e) => {
        communeSelect.value = '';
        loadCommunes(e.target.value);
    });
</script>
