@extends('layouts.design')

@section('content')
    {{-- Categories --}}
    <nav class="bg-gradient-to-r from-blue-500 to-cyan-500 py-4 text-white shadow-lg" aria-label="Categories">
        <div class="pixelify-sans-category container mx-auto flex items-center justify-start gap-3 overflow-x-auto px-4 xl:justify-center">
            <x-category-pill :href="route('articles.index')" :active="empty($filters['category'])">ALL CATEGORIES</x-category-pill>
            @foreach ($categories as $category)
                <x-category-pill :href="route('articles.index', ['category' => $category->id])" :active="($filters['category'] ?? null) == $category->id">{{ $category->name }}</x-category-pill>
            @endforeach
        </div>
    </nav>

    <div class="container mx-auto rounded-3xl border border-blue-300 bg-white p-4 shadow-2xl shadow-blue-950/20 md:p-8">
        <div class="flex flex-col gap-8 lg:flex-row">
            {{-- The articles --}}
            <div class="flex-grow">
                <div class="mb-8 flex flex-col items-start gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <h1 class="press-start-2p-regular text-2xl font-extrabold text-cyan-600 sm:text-4xl">ARTICLES</h1>

                    <div class="flex flex-wrap items-center gap-3">
                        @auth
                            <span class="text-[#0486A2]">Welcome, <strong>{{ Auth::user()->name }}</strong>!</span>
                            <a href="{{ route('articles.create') }}" class="rounded-full bg-[#037A91] px-5 py-2 text-white shadow transition hover:bg-[#02677b]">New Article</a>
                            <a href="{{ route('profile.edit') }}" class="rounded-full border border-[#037A91] px-5 py-2 text-[#037A91] transition hover:bg-cyan-50">Profile</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="rounded-full bg-[#036E86] px-5 py-2 text-white shadow transition hover:bg-[#025a6f]">Logout</button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="rounded-full bg-[#037A91] px-5 py-2 text-white shadow transition hover:bg-[#02677b]">Login</a>
                            <a href="{{ route('register') }}" class="rounded-full bg-[#036E86] px-5 py-2 text-white shadow transition hover:bg-[#025a6f]">Create Account</a>
                        @endauth
                    </div>
                </div>

                <form method="GET" action="{{ route('articles.index') }}" class="mb-8 flex items-center gap-2">
                    @if (! empty($filters['category']))
                        <input type="hidden" name="category" value="{{ $filters['category'] }}">
                    @endif
                    <input type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search title or @author..." aria-label="Search"
                           class="w-full flex-grow rounded-full border border-gray-300 px-5 py-3 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <button type="submit" class="rounded-full bg-blue-600 px-6 py-3 font-semibold text-white shadow transition hover:bg-blue-700">Filter</button>
                </form>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    @forelse ($articles as $article)
                        <x-article-card :article="$article" :eager="$loop->index < 2" />
                    @empty
                        <p class="col-span-full py-10 text-center text-lg text-gray-600">No articles found.</p>
                    @endforelse
                </div>

                <div class="mt-8">{{ $articles->links('pagination::tailwind') }}</div>
            </div>

            {{-- The sidebar --}}
            <aside class="flex w-full flex-col gap-6 lg:w-1/3" aria-label="Highlights">
                <x-highlight title="Latest Post" :article="$latestPost" colors="from-yellow-400 to-orange-500" empty="No posts available." />
                <x-highlight title="Trending Post" :article="$trendingPost" colors="from-purple-400 to-pink-500" empty="No trending posts available." :likes="true" />
            </aside>
        </div>
    </div>
@endsection
