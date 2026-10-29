{{-- The bar at the top of every page: logo, search, light/dark switch and the visitor's account. --}}
<header class="sticky top-0 z-50 border-b border-line bg-paper/90 backdrop-blur-md">
    <div class="mx-auto flex max-w-6xl items-center gap-3 px-4 py-3">
        <a href="{{ route('articles.index') }}" class="flex items-center gap-3" aria-label="My Blog, home">
            <span class="logo-mark" aria-hidden="true"><i></i><i class="off"></i><i></i><i></i><i></i><i></i><i class="off"></i><i></i><i class="off"></i></span>
            <span class="pixel-title text-[0.8rem] tracking-tight text-ink sm:text-sm">MY BLOG</span>
        </a>

        <form method="GET" action="{{ route('articles.index') }}" role="search" aria-label="Quick search" class="ml-4 hidden max-w-sm flex-1 md:block">
            <label for="header-search" class="sr-only">Search articles</label>
            <div class="relative">
                <svg class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-ink-soft" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                <input id="header-search" type="search" name="search" value="{{ request('search') }}" placeholder="Search title or @author" class="field !rounded-full !py-2 !pl-10 text-sm">
            </div>
        </form>

        <div class="ml-auto flex items-center gap-2">
            <button type="button" data-theme-toggle class="btn btn-ghost !h-10 !w-10 !p-0" aria-label="Dark mode" aria-pressed="false" title="Light / dark">
                <svg class="theme-moon h-[1.15rem] w-[1.15rem]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8Z"/></svg>
                <svg class="theme-sun hidden h-[1.15rem] w-[1.15rem]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/></svg>
            </button>

            @auth
                <a href="{{ route('articles.create') }}" class="btn btn-primary !px-4 !py-2">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                    <span class="hidden sm:inline">Write</span><span class="sr-only sm:hidden">Write an article</span>
                </a>
                <details class="relative" data-menu>
                    <summary class="flex list-none items-center gap-2 rounded-full p-0.5 [&::-webkit-details-marker]:hidden" aria-label="Your account">
                        <x-avatar :name="Auth::user()->name" />
                    </summary>
                    <div class="absolute right-0 mt-2 w-56 overflow-hidden rounded-2xl border border-line bg-surface p-1.5 shadow-lg">
                        <p class="truncate px-3 py-2 text-sm font-semibold">{{ Auth::user()->name }}</p>
                        <a href="{{ route('authors.show', Auth::id()) }}" class="block rounded-xl px-3 py-2 text-sm hover:bg-surface-2">My articles</a>
                        <a href="{{ route('profile.edit') }}" class="block rounded-xl px-3 py-2 text-sm hover:bg-surface-2">Profile</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full rounded-xl px-3 py-2 text-left text-sm hover:bg-surface-2">Log out</button>
                        </form>
                    </div>
                </details>
            @else
                <a href="{{ route('login') }}" class="btn btn-ghost !px-4 !py-2">Login</a>
                <a href="{{ route('register') }}" class="btn btn-primary !hidden !px-4 !py-2 sm:!inline-flex">Create Account</a>
            @endauth
        </div>
    </div>
</header>
