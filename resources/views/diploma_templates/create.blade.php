<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nueva plantilla</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6">
                    <form action="{{ route('diploma-templates.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @include('diploma_templates._form', ['template' => $template])
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
