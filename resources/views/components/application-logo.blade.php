@if ($appLogo)
    <img src="{{ asset('storage/' . $appLogo) }}"
         alt="Logo OTEC"
         class="h-8 w-auto">
@else
    <span class="text-lg font-bold">OTEC</span>
@endif