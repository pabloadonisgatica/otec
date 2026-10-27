@extends('questionnaires.public.layout')

@section('content')
    <div class="bg-white shadow-sm rounded-xl border border-gray-200 p-8 text-center">
        <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-green-100 text-green-700">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <h1 class="text-lg font-semibold text-gray-900">¡Gracias por responder!</h1>
        <p class="mt-2 text-sm text-gray-600">Tus respuestas a «{{ $questionnaire->name }}» quedaron registradas.</p>
        <p class="mt-1 text-sm text-gray-500">Ya puedes cerrar esta página.</p>
    </div>
@endsection
