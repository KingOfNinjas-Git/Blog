<!DOCTYPE html>
<html lang="en">

<head>
    <title>@yield('title', 'My Blog | Stories, ideas and tips')</title>
    @include('partials.head')
</head>

<body class="min-h-screen">
    <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[100] focus:rounded-full focus:bg-brand focus:px-5 focus:py-2 focus:font-semibold focus:text-on-brand">Skip to the content</a>
    @include('partials.demo-bar')
    @include('partials.site-header')

    <main id="main-content" tabindex="-1" class="outline-none">
        @if (session('success') || session('message'))
            <p role="status" data-autohide class="fixed left-1/2 top-20 z-[70] w-fit max-w-[calc(100%-2rem)] -translate-x-1/2 rounded-full bg-ink px-6 py-3 text-sm font-semibold text-paper shadow-xl">{{ session('success') ?? session('message') }}</p>
        @endif

        @yield('content')
    </main>

    @include('partials.site-footer')
</body>

</html>
