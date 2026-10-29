<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <title>{{ config('app.name', 'My Blog') }}</title>
        @include('partials.head')
    </head>
    <body class="min-h-screen">
        @include('partials.demo-bar')
        <div class="relative flex min-h-screen flex-col items-center justify-center overflow-hidden px-4 py-10">
            <div class="pixel-bg pointer-events-none absolute inset-0" aria-hidden="true"></div>
            <main id="main-content" class="relative w-full max-w-md">
                <h1 class="sr-only">{{ $heading ?? 'Your My Blog account' }}</h1>
                <a href="{{ route('articles.index') }}" class="mb-6 flex items-center justify-center gap-3" aria-label="My Blog, home">
                    <span class="logo-mark" aria-hidden="true"><i></i><i class="off"></i><i></i><i></i><i></i><i></i><i class="off"></i><i></i><i class="off"></i></span>
                    <span class="pixel-title text-sm">MY BLOG</span>
                </a>
                <div class="panel p-6 sm:p-8">
                    {{ $slot }}
                </div>
                <p class="mt-6 text-center text-sm text-ink-soft"><a href="{{ route('articles.index') }}" class="font-semibold hover:text-brand">&larr; Back to the articles</a></p>
            </main>
        </div>
    </body>
</html>
