@extends('layouts.design')

@section('title', 'Edit article | My Blog')

@section('content')
    <div class="mx-auto max-w-3xl px-4 py-10 sm:py-14">
        <p class="kicker">Editing</p>
        <h1 class="mt-2 text-3xl font-extrabold tracking-tight sm:text-4xl">Edit article</h1>
        <div class="panel mt-8 p-6 sm:p-8">
            @include('articles._form')
        </div>
    </div>
@endsection
