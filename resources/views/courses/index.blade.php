<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Cursos
            </h2>

            <a href="{{ route('courses.create') }}"
               class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Nuevo curso
            </a>
        </div>
    </x-slot>

    <div class="py-6" x-data="courseDetailModal()" x-cloak>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <x-breadcrumb :items="$breadcrumbs" />

            @if (session('status'))
                <div class="mb-4 p-3 rounded bg-green-50 text-green-700">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6">

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="text-left text-gray-600 border-b">
                            <tr>
                                <th class="py-2 pr-4">Curso</th>
                                
                                <th class="py-2 pr-4">Tipo</th>
                                <th class="py-2 pr-4">Modalidades</th>
                                
                                <th class="py-2 pr-4 text-right">Horas</th>
                                <th class="py-2 pr-4 text-right">Acciones</th>
                            </tr>

                            </thead>
                            <tbody class="divide-y">
                            @php
                                    $typeLabels = [
                                        'sence' => 'SENCE',
                                        'licitacion' => 'Licitación',
                                        'privado' => 'Privado',
                                    ];

                                    $modLabels = [
                                        'presencial' => 'Presencial',
                                        'elearning_sync' => 'E-learning Sync',
                                        'elearning_async' => 'E-learning Async',
                                        'distance_self' => 'Autoaprendizaje',
                                    ];
                                @endphp

                                @forelse($courses as $course)
                                    @php
                                        $modalities = $course->instruction_modalities ?? [];
                                        if (!is_array($modalities)) $modalities = [];

                                        $sd = $course->start_date?->format('d-m-Y');
                                        $ed = $course->end_date?->format('d-m-Y');
                                    @endphp

                                    <tr class="hover:bg-gray-50">
                                        {{-- Curso + folio --}}
                                        <td class="py-3 pr-4">
                                            <div class="font-medium text-gray-900">
                                                {{ $course->name }}
                                            </div>

                                            <div class="text-gray-500">
                                                <span class="font-medium">{{ $course->folio ?? '—' }}</span>
                                               
                                            </div>
                                        </td>

                                        

                                        {{-- Tipo --}}
                                        <td class="py-3 pr-4">
                                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-700">
                                                {{ $typeLabels[$course->course_type] ?? ($course->course_type ?: '—') }}
                                            </span>
                                        </td>

                                        {{-- Modalidades --}}
                                        <td class="py-3 pr-4 text-gray-700">
                                            @if(count($modalities))
                                                {{ collect($modalities)->map(fn($m) => $modLabels[$m] ?? $m)->join(' · ') }}
                                            @else
                                                —
                                            @endif
                                        </td>

                                       

                                        {{-- Horas --}}
                                        <td class="py-3 pr-4 text-right text-gray-900 font-medium">
                                            {{ $course->hours ?? '—' }}
                                        </td>

                                        {{-- Acciones --}}
                                        <td class="py-3 pr-4 text-right">
                                            <button type="button"
                                                    class="text-gray-700 hover:text-gray-900 font-medium"
                                                    @click="open(@js($course->id))">
                                                Ver
                                            </button>

                                            <a href="{{ route('courses.edit', $course) }}"
                                            class="ml-3 text-indigo-600 hover:text-indigo-900 font-medium">
                                                Editar
                                            </a>

                                            <form action="{{ route('courses.destroy', $course) }}"
                                                method="POST"
                                                class="inline"
                                                onsubmit="return confirm('¿Eliminar este curso?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="ml-3 text-red-600 hover:text-red-800 font-medium">
                                                    Eliminar
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="py-8 text-center text-gray-500">
                                            No hay cursos aún.
                                        </td>
                                    </tr>
                                @endforelse

                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $courses->links() }}
                    </div>

                </div>
            </div>

        </div>

        {{-- MODAL DETALLE CURSO --}}
        <div x-show="isOpen"
             x-transition.opacity
             class="fixed inset-0 z-50 flex items-center justify-center"
             aria-modal="true"
             role="dialog">

            {{-- Backdrop --}}
            <div class="absolute inset-0 bg-black/40" @click="close()"></div>

            {{-- Panel --}}
            <div class="relative w-full  mx-4 bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="max-h-[85vh] overflow-y-auto">
                    <template x-if="selectedId !== null">
                        <div>
                            @foreach($courses as $courseForModal)
                                <template x-if="selectedId === @js($courseForModal->id)">
                                    <div>
                                        @include('courses.partials.detail', ['course' => $courseForModal])
                                    </div>
                                </template>
                            @endforeach
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <script>
            function courseDetailModal() {
                return {
                    isOpen: false,
                    selectedId: null,
                    open(id) {
                        this.selectedId = id;
                        this.isOpen = true;
                        document.body.classList.add('overflow-hidden');
                    },
                    close() {
                        this.isOpen = false;
                        this.selectedId = null;
                        document.body.classList.remove('overflow-hidden');
                    },
                }
            }
        </script>

    </div>
</x-app-layout>
