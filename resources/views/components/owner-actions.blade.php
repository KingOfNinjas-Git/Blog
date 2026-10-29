@props(['article'])
{{-- Edit and delete, shown only to the person who wrote the article. --}}
@if ($article->isWrittenBy(auth()->user()))
    <div {{ $attributes->class(['flex items-center gap-1']) }}>
        <a href="{{ route('articles.edit', $article) }}" class="rounded-full p-2 text-ink-soft transition hover:bg-brand-soft hover:text-brand" aria-label="Edit this article" title="Edit">
            <svg class="h-[1.05rem] w-[1.05rem]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
        </a>
        <form method="POST" action="{{ route('articles.destroy', $article) }}" onsubmit="return confirm('Are you sure you want to delete this article?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="rounded-full p-2 text-ink-soft transition hover:bg-danger-soft hover:text-danger" aria-label="Delete this article" title="Delete">
                <svg class="h-[1.05rem] w-[1.05rem]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6M10 11v6M14 11v6"/></svg>
            </button>
        </form>
    </div>
@endif
