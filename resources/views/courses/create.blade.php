<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Nuevo curso
        </h2>
    </x-slot>

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

        $initialCourseType = old('course_type', 'privado');

        $initialModalities = old('instruction_modalities', []);
        if (!is_array($initialModalities)) $initialModalities = [];

        $initialSenceApprovalDate = old('sence_approval_date', null);
    @endphp

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            <x-breadcrumb :items="$breadcrumbs" />

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6">

                    <form method="POST"
                        action="{{ route('courses.store') }}"
                        x-data="courseForm()"
                        x-init="
                            contents = [{
                                activity: '',
                                content: '',
                                hours_theoretical: 0,
                                hours_practical: 0,
                                hours_elearning: 0,
                            }];
                            courseType = 'privado';
                            modalities = [];
                            senceApprovalDate = null;
                            init();
                        "
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

    <script>
        function courseForm() {
            return {
                contents: [],
                courseType: null,
                modalities: [],
                senceApprovalDate: null,

                init() {
                    if (!Array.isArray(this.contents)) this.contents = [];

                    if (this.contents.length === 0) {
                        this.contents.push({
                            activity: '',
                            content: '',
                            hours_theoretical: 0,
                            hours_practical: 0,
                            hours_elearning: 0,
                        });
                    }
                },

                get hasSelfLearning() {
                    return Array.isArray(this.modalities) && this.modalities.includes('distance_self');
                },

                get senceExpiryText() {
                    if (this.courseType !== 'sence') return '—';
                    if (!this.senceApprovalDate) return 'Ingrese fecha de aprobación';

                    const d = new Date(this.senceApprovalDate);
                    if (isNaN(d.getTime())) return 'Fecha inválida';

                    d.setFullYear(d.getFullYear() + 4);

                    const yyyy = d.getFullYear();
                    const mm = String(d.getMonth() + 1).padStart(2, '0');
                    const dd = String(d.getDate()).padStart(2, '0');
                    return `${dd}-${mm}-${yyyy}`;
                },

                toNum(v) {
                    const n = parseFloat(v);
                    return isNaN(n) ? 0 : n;
                },

                get totalTheoretical() {
                    return this.contents.reduce((sum, r) => sum + this.toNum(r.hours_theoretical), 0);
                },
                get totalPractical() {
                    return this.contents.reduce((sum, r) => sum + this.toNum(r.hours_practical), 0);
                },
                get totalElearning() {
                    return this.contents.reduce((sum, r) => sum + this.toNum(r.hours_elearning), 0);
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
