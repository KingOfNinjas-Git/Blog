@if (config('demo.enabled'))
    <style>body { padding-top: 38px !important; }</style>
    <div id="demo-bar" role="note" style="position:fixed;top:0;left:0;right:0;z-index:9999;background:#06283d;color:#d9f4ff;font:600 13px/1.5 system-ui,sans-serif;text-align:center;padding:8px 14px;border-bottom:2px solid #06b6d4;">
        Portfolio demo by <a href="{{ config('demo.portfolio_url') }}" style="color:#fff;text-decoration:underline;">Francisco</a>:
        create a free account to like, comment and write articles. Everything is cleared regularly, so <strong>use a made-up email</strong>.
    </div>
    {{-- On phones the notice wraps to several lines: keep the page just below it, whatever its height. --}}
    <script>
        (function () {
            var bar = document.getElementById('demo-bar');
            function fit() { document.body.style.setProperty('padding-top', bar.offsetHeight + 'px', 'important'); }
            fit();
            window.addEventListener('resize', fit);
            window.addEventListener('load', fit);
        })();
    </script>
@endif
