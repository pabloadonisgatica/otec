{{--
    Acciones de una fila de tabla (patrón de Polaris, Carbon y Atlassian):
    1-2 acciones frecuentes visibles con texto + menú "⋯" con el resto.
    La acción principal (abrir/editar) va en el nombre de la fila, no aquí.
    Las acciones destructivas van siempre en el menú, al final y separadas.

    Uso:
    <x-ui.row-actions>
        <x-ui.row-actions.link :href="route('x.share', $item)">Compartir</x-ui.row-actions.link>

        <x-slot:menu>
            <x-ui.row-actions.item :href="route('x.edit', $item)">Editar</x-ui.row-actions.item>
            <x-ui.row-actions.item :action="route('x.duplicate', $item)">Duplicar</x-ui.row-actions.item>
            <x-ui.row-actions.divider />
            <x-ui.row-actions.item :action="route('x.destroy', $item)" method="DELETE" danger
                                   confirm="¿Eliminar? No se puede deshacer.">Eliminar</x-ui.row-actions.item>
        </x-slot:menu>
    </x-ui.row-actions>

    El menú se dibuja fuera de la tabla (x-teleport) para que el scroll horizontal
    de la tabla no lo corte, y se abre hacia arriba si no cabe abajo.
    Todo el estado interno usa el prefijo "ra" para no tapar funciones de la
    página (por ejemplo, el open() de los modales de detalle).
--}}
@props(['label' => 'Más acciones'])

<div {{ $attributes->merge(['class' => 'flex items-center justify-end gap-2']) }}
     x-data="{
         raOpen: false,
         raTop: 0,
         raLeft: 0,
         raToggle() {
             if (this.raOpen) { this.raOpen = false; return; }
             const box = this.$refs.raTrigger.getBoundingClientRect();
             this.raTop = box.bottom + 4;
             this.raLeft = Math.max(8, box.right - 192); // w-48, alineado al borde derecho del botón
             this.raOpen = true;
             this.$nextTick(() => {
                 const height = this.$refs.raMenu.offsetHeight;
                 if (this.raTop + height > window.innerHeight - 8) {
                     this.raTop = Math.max(8, box.top - height - 4);
                 }
                 this.$refs.raMenu.querySelector('[role=menuitem]')?.focus();
             });
         },
         raClose(focusTrigger = false) {
             this.raOpen = false;
             if (focusTrigger) this.$refs.raTrigger.focus();
         },
         raMove(step) {
             const items = [...this.$refs.raMenu.querySelectorAll('[role=menuitem]')];
             const index = items.indexOf(document.activeElement);
             items[(index + step + items.length) % items.length]?.focus();
         },
     }"
     @keydown.escape.window="raOpen && raClose(true)"
     @scroll.window="raClose()"
     @resize.window="raClose()">

    {{ $slot }}

    @isset($menu)
        <button type="button" x-ref="raTrigger" @click="raToggle()"
                aria-haspopup="menu" :aria-expanded="raOpen.toString()"
                aria-label="{{ $label }}" title="{{ $label }}"
                class="inline-flex h-8 w-8 items-center justify-center rounded-md text-gray-500 hover:bg-gray-100 hover:text-gray-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">
            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path d="M4 10a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Zm7.5 0a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0ZM19 10a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z"/>
            </svg>
        </button>

        <template x-teleport="body">
            <div x-ref="raMenu" x-show="raOpen" x-cloak
                 x-transition:enter="transition ease-out duration-100"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click.outside="raClose()"
                 @keydown.arrow-down.prevent="raMove(1)"
                 @keydown.arrow-up.prevent="raMove(-1)"
                 :style="`position: fixed; top: ${raTop}px; left: ${raLeft}px;`"
                 role="menu" aria-label="{{ $label }}"
                 class="z-50 w-48 origin-top-right rounded-md bg-white py-1 text-sm shadow-lg ring-1 ring-black ring-opacity-5">
                {{ $menu }}
            </div>
        </template>
    @endisset
</div>
