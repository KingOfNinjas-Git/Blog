@extends('layouts.design')

@section('content')
    <div class="mx-auto max-w-2xl rounded-3xl border border-blue-300 bg-white p-6 shadow-2xl shadow-blue-950/20 md:p-8">
        <h1 class="press-start-2p-regular mb-6 text-xl font-bold text-cyan-600 sm:text-2xl">Edit Article</h1>
        @include('articles._form')
    </div>
@endsection
