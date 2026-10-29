@if (config('demo.enabled'))
    <aside id="demo-bar" aria-label="About this demo" class="bg-[#0b1d33] px-4 py-2 text-center text-[13px] font-semibold leading-snug text-[#dff3ff]">
        Portfolio demo by <a href="{{ config('demo.portfolio_url') }}" class="underline underline-offset-2">Francisco</a>:
        create a free account to like, comment and write articles. Everything is cleared regularly, so <strong>use a made-up email</strong>.
    </aside>
@endif
