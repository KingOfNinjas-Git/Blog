@props(['article', 'eager' => false, 'liked' => false])
{{-- One article in the list: picture, category, title, a short teaser, who wrote it, then likes, comments and (for the author) edit and delete. --}}
@php
    $srcset = \App\Support\ArticleImage::srcset($article->image);
@endphp
<article class="story">
    <div class="story-image">
        @if ($article->image)
            <img src="{{ $srcset ? asset('storage/'.\App\Support\ArticleImage::thumbPath($article->image)) : asset('storage/'.$article->image) }}"
                 @if ($srcset) srcset="{{ $srcset }}" sizes="(min-width: 1152px) 360px, (min-width: 640px) 45vw, 100vw" @endif
                 alt="" width="1000" height="600" decoding="async"
                 @if ($eager) fetchpriority="high" @else loading="lazy" @endif>
        @else
            <div class="flex h-full items-center justify-center bg-gradient-to-br from-brand-soft to-surface-2 pixel-title text-2xl text-brand" aria-hidden="true">&lt;/&gt;</div>
        @endif
    </div>

    <div class="flex flex-1 flex-col gap-3 p-5">
        <div class="flex items-center justify-between gap-2">
            <a href="{{ route('articles.index', ['category' => $article->category_id]) }}" class="badge above hover:underline">{{ $article->category->name }}</a>
            <span class="text-xs font-medium text-ink-soft">{{ $article->readingMinutes() }} min read</span>
        </div>

        <h2 class="text-xl font-bold leading-snug tracking-tight">
            <a href="{{ route('articles.show', $article) }}" class="stretched transition hover:text-brand">{{ $article->title }}</a>
        </h2>
        <p class="line-clamp-3 text-sm leading-6 text-ink-soft">{{ $article->excerpt() }}</p>

        <div class="mt-auto flex items-center gap-2.5 pt-2 text-sm text-ink-soft">
            <x-avatar :name="$article->author->name" />
            <span class="min-w-0 truncate">
                <a href="{{ route('authors.show', $article->author_id) }}" class="above font-semibold text-ink hover:text-brand">{{ $article->author->name }}</a>
                <span aria-hidden="true">&middot;</span>
                <time datetime="{{ $article->created_at->toDateString() }}">{{ $article->created_at->format('M j, Y') }}</time>
            </span>
            @if ($article->reactions_count > 10)
                <span class="badge ml-auto !bg-accent !text-on-accent">Popular</span>
            @endif
        </div>

        <div class="above flex items-center gap-2 border-t border-line pt-3">
            <x-like-button :article="$article" :liked="$liked" />
            <a href="{{ route('articles.show', $article) }}#comments" class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1.5 text-sm font-semibold text-ink-soft transition hover:text-brand" aria-label="{{ $article->comments_count }} {{ Str::plural('comment', $article->comments_count) }}">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linejoin="round" aria-hidden="true"><path d="M21 12a8 8 0 0 1-11.6 7.1L4 20l1-4.6A8 8 0 1 1 21 12Z"/></svg>
                <span aria-hidden="true">{{ $article->comments_count }}</span>
            </a>
            <x-owner-actions :article="$article" class="ml-auto" />
        </div>
    </div>
</article>
