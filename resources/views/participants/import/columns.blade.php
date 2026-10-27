<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Carga masiva de participantes
            </h2>

            <a href="{{ route('participants.import.form') }}"
               class="underline text-sm text-gray-700 hover:text-gray-900">
                Subir otro archivo
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            <x-breadcrumb :items="$breadcrumbs" />

            @include('participants.import._steps', ['current' => 2])

            <form method="POST" action="{{ route('participants.import.columns.save', $token) }}"
                  x-data="{
                      mapping: @js(array_values($mapping)),
                      sample: @js($samples[0] ?? []),
                      labels: @js($fields),
                      required: @js($required),
                      value(field) {
                          return this.mapping
                              .map((target, index) => target === field ? (this.sample[index] ?? '').trim() : '')
                              .filter(v => v !== '')
                              .join(' ');
                      },
                      get missing() {
                          return this.required.filter(field => ! this.mapping.includes(field));
                      },
                  }"
                  class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                @csrf

                {{-- Columnas del archivo --}}
                <x-ui.card class="lg:col-span-2">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <p class="text-sm text-gray-700">
                            <span class="font-medium">{{ $state['file_name'] }}</span>
                            · {{ $totalRows }} {{ $totalRows === 1 ? 'fila' : 'filas' }}
                            · Empresa: <span class="font-medium">{{ $company?->name }}</span>
                        </p>
                        <p class="mt-1 text-xs text-gray-500">
                            Ya asignamos las columnas que reconocimos. Revisa y corrige lo que no calce.
                            Puedes asignar varias columnas al mismo campo (por ejemplo, apellido paterno y materno): se unen en orden.
                        </p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50 text-left text-xs uppercase tracking-wider text-gray-500">
                                <tr>
                                    <th class="px-6 py-3">Columna del archivo</th>
                                    <th class="px-6 py-3">Ejemplos</th>
                                    <th class="px-6 py-3">Campo en el sistema</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($headers as $index => $header)
                                    <tr :class="mapping[{{ $index }}] ? '' : 'bg-gray-50/60'">
                                        <td class="px-6 py-3 font-medium text-gray-800">{{ $header }}</td>
                                        <td class="px-6 py-3 text-gray-500">
                                            @foreach ($samples as $sample)
                                                <div class="max-w-[16rem] truncate">{{ ($sample[$index] ?? '') !== '' ? $sample[$index] : '—' }}</div>
                                            @endforeach
                                        </td>
                                        <td class="px-6 py-3">
                                            <select name="mapping[{{ $index }}]" x-model="mapping[{{ $index }}]"
                                                    class="w-full rounded-md border-gray-300 text-sm">
                                                <option value="">— No importar —</option>
                                                @foreach ($fields as $key => $label)
                                                    <option value="{{ $key }}" @selected(($mapping[$index] ?? '') === $key)>{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </x-ui.card>

                {{-- Vista previa en vivo --}}
                <div class="space-y-4">
                    <x-ui.card class="lg:sticky lg:top-6">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-sm font-semibold text-gray-900">Así quedará el primer participante</h3>
                        </div>

                        <dl class="px-6 py-4 space-y-3 text-sm">
                            @foreach ($fields as $key => $label)
                                <div>
                                    <dt class="text-xs text-gray-500">
                                        {{ $label }}{{ in_array($key, $required, true) ? ' *' : '' }}
                                    </dt>
                                    <dd class="font-medium"
                                        :class="value('{{ $key }}') ? 'text-gray-900' : 'text-gray-400'"
                                        x-text="value('{{ $key }}') || 'Sin asignar'"></dd>
                                </div>
                            @endforeach
                        </dl>

                        <div class="px-6 pb-6 space-y-3">
                            <p x-show="missing.length" x-cloak class="rounded-md bg-amber-50 px-3 py-2 text-sm text-amber-800">
                                Falta asignar: <span x-text="missing.map(field => labels[field]).join(', ')"></span>.
                            </p>

                            @error('mapping')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @enderror

                            <button type="submit" :disabled="missing.length > 0"
                                    class="w-full px-4 py-2 rounded bg-indigo-600 text-white hover:bg-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed">
                                Revisar datos
                            </button>
                        </div>
                    </x-ui.card>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>
