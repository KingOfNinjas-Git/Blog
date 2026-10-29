@extends('layouts.design')

@section('title', $article->title.' | My Blog')

@section('share')
    <meta name="description" content="{{ $article->excerpt(160) }}">
    <meta property="og:type" content="article">
    <meta property="og:site_name" content="My Blog">
    <meta property="og:title" content="{{ $article->title }}">
    <meta property="og:description" content="{{ $article->excerpt(160) }}">
    <meta property="og:url" content="{{ route('articles.show', $article) }}">
    <meta property="og:image" content="{{ $article->image ? asset('storage/'.$article->image) : url('/blog-share.jpg?v=2') }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $article->title }}">
    <meta name="twitter:description" content="{{ $article->excerpt(160) }}">
    <meta name="twitter:image" content="{{ $article->image ? asset('storage/'.$article->image) : url('/blog-share.jpg?v=2') }}">
@endsection

@section('content')
    @php($size = $article->imageSize())
    <div class="progress" aria-hidden="true"><span id="progress-bar"></span></div>

    <article class="mx-auto max-w-4xl px-4 pb-6 pt-8 sm:pt-12" data-article>
        <nav aria-label="Breadcrumb" class="mb-6 text-sm text-ink-soft">
            <ol class="flex flex-wrap items-center gap-2">
                <li><a href="{{ route('articles.index') }}" class="hover:text-brand">Articles</a></li>
                <li aria-hidden="true">/</li>
                <li><a href="{{ route('articles.index', ['category' => $article->category_id]) }}" class="hover:text-brand">{{ ucfirst(strtolower($article->category->name)) }}</a></li>
            </ol>
        </nav>

        <header class="mx-auto max-w-3xl">
            <a href="{{ route('articles.index', ['category' => $article->category_id]) }}" class="badge hover:underline">{{ $article->category->name }}</a>
            <h1 class="mt-4 text-3xl font-extrabold leading-[1.12] tracking-tight sm:text-5xl">{{ $article->title }}</h1>

            <div class="mt-6 flex flex-wrap items-center gap-x-4 gap-y-3">
                <x-avatar :name="$article->author->name" size="lg" />
                <div class="text-sm leading-6">
                    <a href="{{ route('articles.index', ['search' => '@'.$article->author->name]) }}" class="font-bold hover:text-brand">{{ $article->author->name }}</a>
                    <p class="text-ink-soft">
                        <time datetime="{{ $article->created_at->toDateString() }}">{{ $article->created_at->format('F j, Y') }}</time>
                        &middot; {{ $article->readingMinutes() }} min read
                    </p>
                </div>
            </div>
        </header>

        @if ($article->image)
            <figure class="mt-8 overflow-hidden rounded-[2rem] border border-line bg-surface-2 shadow-lg">
                <img src="{{ asset('storage/'.$article->image) }}" alt="{{ $article->title }}" fetchpriority="high" decoding="async"
                     @if ($size) width="{{ $size[0] }}" height="{{ $size[1] }}" @endif class="h-auto w-full">
            </figure>
        @endif

        {{-- Like, comment, share --}}
        <div class="mx-auto mt-6 flex max-w-3xl flex-wrap items-center gap-3 border-y border-line py-4" data-share data-url="{{ route('articles.show', $article) }}" data-title="{{ $article->title }}">
            <x-like-button :article="$article" :liked="$liked" />
            <a href="#comments" class="btn btn-ghost !px-4 !py-1.5 !text-sm">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linejoin="round" aria-hidden="true"><path d="M21 12a8 8 0 0 1-11.6 7.1L4 20l1-4.6A8 8 0 1 1 21 12Z"/></svg>
                {{ $article->comments_count }} {{ Str::plural('comment', $article->comments_count) }}
            </a>
            <div class="ml-auto flex items-center gap-2">
                <button type="button" data-share-native hidden class="btn btn-ghost !px-4 !py-1.5 !text-sm">Share</button>
                <button type="button" data-copy-link class="btn btn-ghost !px-4 !py-1.5 !text-sm">Copy link</button>
                <span data-copy-status role="status" aria-live="polite" class="text-sm font-semibold text-cyan"></span>
            </div>
        </div>

        {{-- The text --}}
        <div class="mx-auto mt-10 max-w-3xl"><div class="read max-w-[40rem]" data-prose>
            @foreach ($article->paragraphs() as $paragraph)
                <p>{!! nl2br(e($paragraph)) !!}</p>
            @endforeach
        </div></div>

        @if ($article->isWrittenBy(auth()->user()))
            <div class="mx-auto mt-10 flex max-w-3xl flex-wrap gap-3">
                <a href="{{ route('articles.edit', $article) }}" class="btn btn-accent">Edit this article</a>
                <form method="POST" action="{{ route('articles.destroy', $article) }}" onsubmit="return confirm('Are you sure you want to delete this article?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-ghost !text-danger hover:!border-danger">Delete</button>
                </form>
            </div>
        @endif

        {{-- Who wrote it --}}
        <aside class="panel mx-auto mt-14 flex max-w-3xl items-center gap-5 !rounded-3xl p-6" aria-label="About the author">
            <x-avatar :name="$article->author->name" size="lg" />
            <div class="min-w-0">
                <p class="kicker !text-xs">Written by</p>
                <p class="text-lg font-bold">{{ $article->author->name }}</p>
                <a href="{{ route('articles.index', ['search' => '@'.$article->author->name]) }}" class="text-sm font-semibold text-brand underline underline-offset-2">More from {{ $article->author->name }}</a>
            </div>
        </aside>

        {{-- Comments --}}
        <section id="comments" class="mx-auto mt-14 max-w-3xl scroll-mt-24" aria-labelledby="comments-title">
            <h2 id="comments-title" class="text-2xl font-extrabold tracking-tight">
                Comments <span class="ml-1 rounded-full bg-brand-soft px-2.5 py-0.5 align-middle text-sm font-bold text-brand">{{ $article->comments_count }}</span>
            </h2>

            <ol class="mt-6 space-y-4">
                @forelse ($article->comments as $comment)
                    <li class="panel flex gap-4 !rounded-2xl p-5">
                        <x-avatar :name="$comment->user->name" />
                        <div class="min-w-0 flex-1">
                            <p class="flex flex-wrap items-baseline gap-x-3 text-sm">
                                <span class="font-bold">{{ $comment->user->name }}</span>
                                <time datetime="{{ $comment->created_at->toIso8601String() }}" class="text-ink-soft">{{ $comment->created_at->diffForHumans() }}</time>
                            </p>
                            <p class="mt-1.5 whitespace-pre-line break-words leading-7 text-ink">{{ $comment->content }}</p>
                        </div>
                    </li>
                @empty
                    <li class="panel !rounded-2xl p-8 text-center text-ink-soft">No comments yet. Be the first to comment!</li>
                @endforelse
            </ol>

            @auth
                <form action="{{ route('articles.comments.store', $article) }}" method="POST" class="panel mt-8 !rounded-3xl p-6">
                    @csrf
                    <label for="content" class="text-lg font-bold">Leave a comment</label>
                    <textarea name="content" id="content" rows="4" required maxlength="1000" placeholder="What did you think?" data-count="#comment-count" class="field mt-3">{{ old('content') }}</textarea>
                    @error('content')<p class="mt-2 text-sm font-semibold text-danger" role="alert">{{ $message }}</p>@enderror
                    <div class="mt-4 flex items-center justify-between gap-3">
                        <span class="text-xs text-ink-soft"><span id="comment-count">0</span> / 1000</span>
                        <button type="submit" class="btn btn-primary">Post comment</button>
                    </div>
                </form>
            @else
                <p class="panel mt-8 !rounded-3xl p-6 text-center text-ink-soft">
                    You must <a href="{{ route('login') }}" class="font-bold text-brand underline underline-offset-2">log in</a> to leave a comment.
                </p>
            @endauth
        </section>
    </article>

    {{-- More to read --}}
    @if ($related->isNotEmpty())
        <section class="mx-auto mt-16 max-w-6xl px-4" aria-labelledby="related-title">
            <h2 id="related-title" class="kicker mb-5">Keep reading</h2>
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($related as $item)
                    <x-article-card :article="$item" />
                @endforeach
            </div>
        </section>
    @endif
@endsection
