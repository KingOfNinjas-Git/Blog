@props(['article', 'liked' => false])
{{-- The heart: a small form that likes the article (a signed-in visitor can like once). --}}
<form method="POST" action="{{ route('articles.like', $article) }}" {{ $attributes }}>
    @csrf
    <button type="submit" @disabled($liked)
            class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1.5 text-sm font-semibold transition {{ $liked ? 'border-transparent bg-danger-soft text-danger' : 'border-line bg-surface text-ink-soft hover:border-danger hover:text-danger' }}">
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="{{ $liked ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2.2" stroke-linejoin="round" aria-hidden="true"><path d="M12 21s-7.5-4.6-9.4-9.3C1.2 8.2 3.2 5 6.4 5c2 0 3.5 1.1 4.3 2.4h.6C12.1 6.1 13.6 5 15.6 5c3.2 0 5.2 3.2 3.8 6.7C19.5 16.4 12 21 12 21Z"/></svg>
        <span class="sr-only">{{ $liked ? 'You liked this article,' : 'Like this article,' }} </span><span>{{ $article->reactions_count }}</span>
    </button>
</form>
