<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Nuevo curso
        </h2>
    </x-slot>

<div class="py-6">
    <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            <x-breadcrumb :items="$breadcrumbs" />

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6">

                    <form method="POST"
                        action="{{ route('courses.store') }}"
                        x-data="courseForm()"
                        class="space-y-6">
                        @csrf

                        @include('courses.partials.form', ['course' => null])

                        <div class="flex items-center justify-end gap-2 pt-2">
                            <a href="{{ route('courses.index') }}"
                            class="px-4 py-2 rounded border border-gray-300 text-gray-700 hover:bg-gray-50">
                                Cancelar
                            </a>
                            <button type="submit"
                                    class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-500">
                                Guardar
                            </button>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>
    @php
    $initialContents = old('contents', [
        [
            'activity' => '',
            'content' => '',
            'hours_theoretical' => 0,
            'hours_practical' => 0,
            'hours_elearning' => 0,
        ]
    ]);
@endphp

<script>
function courseForm() {
    return {
        contents: @json($initialContents),

        get totalTheoretical() {
            return this.contents.reduce((sum, r) => sum + (parseInt(r.hours_theoretical || 0, 10) || 0), 0);
        },
        get totalPractical() {
            return this.contents.reduce((sum, r) => sum + (parseInt(r.hours_practical || 0, 10) || 0), 0);
        },
        get totalElearning() {
            return this.contents.reduce((sum, r) => sum + (parseInt(r.hours_elearning || 0, 10) || 0), 0);
        },
        get totalHours() {
            return this.totalTheoretical + this.totalPractical + this.totalElearning;
        },

        addContent() {
            this.contents.push({
                activity: '',
                content: '',
                hours_theoretical: 0,
                hours_practical: 0,
                hours_elearning: 0,
            });
        },

        removeContent(index) {
            this.contents.splice(index, 1);
        },

    }
}
</script>



</x-app-layout>
