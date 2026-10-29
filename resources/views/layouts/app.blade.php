<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <title>{{ config('app.name', 'My Blog') }}</title>
        @include('partials.head')
    </head>
    <body class="min-h-screen">
        @include('partials.demo-bar')
        @include('partials.site-header')

        <main id="main-content" tabindex="-1" class="mx-auto max-w-3xl px-4 py-10 outline-none sm:py-14">
            @isset($header)
                <div class="mb-8">{{ $header }}</div>
            @endisset

            {{ $slot }}
        </main>

        @include('partials.site-footer')
    </body>
</html>
