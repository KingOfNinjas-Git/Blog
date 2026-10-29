@props(['article', 'liked' => false])
{{-- The newest article, big, at the top of the first page. --}}
@php
    $srcset = \App\Support\ArticleImage::srcset($article->image);
@endphp
<article class="story lg:grid lg:grid-cols-[1.35fr_1fr]" style="border-radius: 2rem">
    <div class="story-image relative !aspect-[16/10] lg:!aspect-auto lg:min-h-[22rem]">
        @if ($article->image)
            <img src="{{ asset('storage/'.$article->image) }}"
                 @if ($srcset) srcset="{{ $srcset }}" sizes="(min-width: 1152px) 640px, (min-width: 1024px) 55vw, 100vw" @endif
                 alt="" width="1000" height="600" decoding="async" fetchpriority="high" class="absolute inset-0">
        @else
            <div class="flex h-full items-center justify-center bg-gradient-to-br from-brand-soft to-surface-2 pixel-title text-3xl text-brand" aria-hidden="true">&lt;/&gt;</div>
        @endif
        <span class="absolute left-4 top-4 rounded-full bg-accent px-3.5 py-1.5 font-[var(--font-pixel-sm)] text-sm uppercase tracking-widest text-on-accent shadow-md">Latest</span>
    </div>

    <div class="flex flex-col gap-4 p-6 sm:p-8 lg:p-10">
        <div class="flex items-center gap-3">
            <a href="{{ route('articles.index', ['category' => $article->category_id]) }}" class="badge above hover:underline">{{ $article->category->name }}</a>
            <span class="text-xs font-medium text-ink-soft">{{ $article->readingMinutes() }} min read</span>
        </div>

        <h2 class="text-2xl font-extrabold leading-tight tracking-tight sm:text-3xl lg:text-[2.1rem]">
            <a href="{{ route('articles.show', $article) }}" class="stretched transition hover:text-brand">{{ $article->title }}</a>
        </h2>
        <p class="line-clamp-4 leading-7 text-ink-soft">{{ $article->excerpt(240) }}</p>

        <div class="flex items-center gap-2.5 text-sm text-ink-soft">
            <x-avatar :name="$article->author->name" />
            <span>
                <a href="{{ route('articles.index', ['search' => '@'.$article->author->name]) }}" class="above font-semibold text-ink hover:text-brand">{{ $article->author->name }}</a>
                <span aria-hidden="true">&middot;</span>
                <time datetime="{{ $article->created_at->toDateString() }}">{{ $article->created_at->format('M j, Y') }}</time>
            </span>
        </div>

        <div class="above mt-auto flex flex-wrap items-center gap-3 pt-2">
            <a href="{{ route('articles.show', $article) }}" class="btn btn-primary">Read the article <span aria-hidden="true">&rarr;</span></a>
            <x-like-button :article="$article" :liked="$liked" />
            <x-owner-actions :article="$article" />
        </div>
    </div>
</article>
