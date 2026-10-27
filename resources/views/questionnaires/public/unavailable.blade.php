@extends('questionnaires.public.layout')

@section('content')
    <div class="bg-white shadow-sm rounded-xl border border-gray-200 p-8 text-center">
        <h1 class="text-lg font-semibold text-gray-900">{{ $questionnaire->name }}</h1>
        <p class="mt-3 text-sm text-gray-600">{{ $reason }}</p>
    </div>
@endsection
