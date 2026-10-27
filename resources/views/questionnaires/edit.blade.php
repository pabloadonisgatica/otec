<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $title }}</h2>

            <a href="{{ route('questionnaires.index') }}" class="underline text-sm text-gray-700 hover:text-gray-900">Volver</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <x-breadcrumb :items="$breadcrumbs" />

            @include('questionnaires._tabs', ['current' => 'general'])

            @include('questionnaires._form')
        </div>
    </div>
</x-app-layout>
