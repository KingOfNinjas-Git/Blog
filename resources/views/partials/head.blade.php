{{-- Everything in <head> that every page shares: fonts, the saved light/dark choice (applied before the page paints, so there is no flash) and the styles. --}}
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="color-scheme" content="light dark">
<script>
    (function () {
        try {
            var t = localStorage.getItem('theme') || (matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            document.documentElement.dataset.theme = t;
        } catch (e) { document.documentElement.dataset.theme = 'light'; }
    })();
</script>
@hasSection('share')
    @yield('share')
    <link rel="icon" href="/favicon.ico?v=2" sizes="any">
    <link rel="icon" type="image/png" sizes="192x192" href="/icon-192.png?v=2">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png?v=2">
@else
    @include('partials.share-head')
@endif
@vite(['resources/css/app.css', 'resources/js/app.js'])
