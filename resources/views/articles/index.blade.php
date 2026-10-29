@extends('layouts.design')

@section('title', 'My Blog | Stories, ideas and tips')

@section('content')
    @php
        // Links that keep the current search / category / order and change one thing
        $link = fn (array $change) => route('articles.index', array_filter(
            array_merge($filters, ['sort' => $sort === 'newest' ? null : $sort], $change),
            fn ($v) => filled($v) && $v !== 'newest',
        ));
        $filtering = filled($filters['search'] ?? null) || filled($filters['category'] ?? null);
        $activeCategory = $categories->firstWhere('id', (int) ($filters['category'] ?? 0));
    @endphp

    {{-- Hero --}}
    <section class="relative overflow-hidden border-b border-line">
        <div class="pixel-bg pointer-events-none absolute inset-0" aria-hidden="true"></div>
        <div class="relative mx-auto grid max-w-6xl gap-8 px-4 pb-10 pt-12 md:grid-cols-[1.5fr_1fr] md:items-end md:pb-14 md:pt-16">
            <div>
                <p class="kicker">Stories &middot; Ideas &middot; Tips</p>
                <h1 class="mt-3 text-4xl font-extrabold leading-[1.05] tracking-tight sm:text-5xl lg:text-6xl">
                    Read, <span class="text-brand">react</span>,<br class="hidden sm:block"> write your own.
                </h1>
                <p class="mt-5 max-w-xl text-lg leading-8 text-ink-soft">Short reads on games, drama, weather and more. Like what you read, join the conversation, then publish something of your own.</p>

                <form method="GET" action="{{ route('articles.index') }}" role="search" aria-label="Search articles" class="mt-7 flex max-w-xl gap-2">
                    @if (! empty($filters['category']))<input type="hidden" name="category" value="{{ $filters['category'] }}">@endif
                    @if ($sort !== 'newest')<input type="hidden" name="sort" value="{{ $sort }}">@endif
                    <label for="search" class="sr-only">Search</label>
                    <input id="search" type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search title or @author..." class="field !rounded-full !py-3.5 !pl-5">
                    <button type="submit" class="btn btn-primary !px-6">Search</button>
                </form>
            </div>

            <dl class="grid grid-cols-3 gap-3 md:grid-cols-1" aria-label="The blog in numbers">
                @foreach ([['articles', 'Articles'], ['writers', 'Writers'], ['likes', 'Likes']] as [$key, $label])
                    <div class="panel flex items-center justify-between gap-3 !rounded-2xl px-4 py-3 md:px-5">
                        <dd class="pixel-title text-lg text-brand md:text-xl">{{ number_format($stats[$key]) }}</dd>
                        <dt class="text-sm font-semibold text-ink-soft">{{ $label }}</dt>
                    </div>
                @endforeach
            </dl>
        </div>
    </section>

    <div class="mx-auto max-w-6xl px-4 py-10">
        {{-- Categories and order --}}
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <nav class="-mx-4 flex gap-2 overflow-x-auto px-4 pb-1 lg:mx-0 lg:flex-wrap lg:overflow-visible lg:px-0" aria-label="Categories">
                <a href="{{ $link(['category' => null]) }}" class="chip" @if (empty($filters['category'])) aria-current="page" @endif>All</a>
                @foreach ($categories as $category)
                    <a href="{{ $link(['category' => $category->id]) }}" class="chip" @if (($filters['category'] ?? null) == $category->id) aria-current="page" @endif>
                        {{ ucfirst(strtolower($category->name)) }}<span class="chip-count">{{ $category->articles_count }}</span>
                    </a>
                @endforeach
            </nav>

            <div class="flex shrink-0 items-center gap-2 text-sm" role="group" aria-label="Order">
                <span class="font-semibold text-ink-soft">Sort</span>
                @foreach (['newest' => 'Newest', 'popular' => 'Most liked', 'discussed' => 'Most discussed'] as $key => $label)
                    <a href="{{ $link(['sort' => $key]) }}" class="chip !px-3 !py-1.5 !text-xs {{ $sort === $key ? 'is-active' : '' }}" @if ($sort === $key) aria-current="true" @endif>{{ $label }}</a>
                @endforeach
            </div>
        </div>

        @if ($filtering)
            <p class="mt-6 text-ink-soft" role="status">
                Showing
                @if (filled($filters['search'] ?? null)) results for <strong class="text-ink">&ldquo;{{ $filters['search'] }}&rdquo;</strong>@endif
                @if ($activeCategory) in <strong class="text-ink">{{ ucfirst(strtolower($activeCategory->name)) }}</strong>@endif
                &middot; <a href="{{ route('articles.index') }}" class="font-semibold text-brand underline underline-offset-2">Clear</a>
            </p>
        @endif

        {{-- The newest article, big --}}
        @if ($featured)
            <div class="mt-8">
                <x-featured-card :article="$featured" :liked="in_array($featured->id, $likedIds, true)" />
            </div>
        @endif

        {{-- Trending --}}
        @if ($trending->isNotEmpty())
            <section class="mt-14" aria-labelledby="trending-title">
                <h2 id="trending-title" class="kicker mb-5">Trending now</h2>
                <ol class="grid gap-4 md:grid-cols-3">
                    @foreach ($trending as $article)
                        <li class="panel relative flex items-start gap-4 !rounded-2xl p-4 transition hover:-translate-y-0.5 hover:shadow-lg">
                            <span class="pixel-title text-2xl text-accent [-webkit-text-stroke:1px_var(--ink)]" aria-hidden="true">{{ $loop->iteration }}</span>
                            <div class="min-w-0">
                                <a href="{{ route('articles.show', $article) }}" class="line-clamp-2 font-bold leading-snug after:absolute after:inset-0 hover:text-brand">{{ $article->title }}</a>
                                <p class="mt-1.5 text-xs font-medium text-ink-soft">{{ $article->author->name }} &middot; {{ $article->reactions_count }} {{ Str::plural('like', $article->reactions_count) }}</p>
                            </div>
                        </li>
                    @endforeach
                </ol>
            </section>
        @endif

        {{-- The articles --}}
        <section class="{{ $featured || $trending->isNotEmpty() ? 'mt-14' : 'mt-8' }}" aria-labelledby="list-title">
            <h2 id="list-title" class="kicker mb-5">{{ $featured ? 'More articles' : 'Articles' }}</h2>

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @forelse ($items as $article)
                    <x-article-card :article="$article" :eager="! $featured && $loop->index < 2" :liked="in_array($article->id, $likedIds, true)" />
                @empty
                    @unless ($featured)
                        <div class="panel col-span-full p-10 text-center">
                            <p class="pixel-title text-2xl text-brand" aria-hidden="true">?</p>
                            <p class="mt-4 text-lg font-semibold">No articles found.</p>
                            <p class="mt-1 text-ink-soft">Try another word, or look at everything.</p>
                            <a href="{{ route('articles.index') }}" class="btn btn-primary mt-6">Show all articles</a>
                        </div>
                    @endunless
                @endforelse
            </div>
        </section>

        <x-pager :paginator="$articles" />
    </div>
@endsection
