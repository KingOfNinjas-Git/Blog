@props(['article', 'eager' => false])
{{-- One article in the list: picture, title, author and category, then like / comments / date and, for the author, edit and delete. --}}
<article class="flex flex-col overflow-hidden rounded-2xl border border-blue-200 bg-blue-50 text-left shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-xl">
    @if ($article->image)
        <a href="{{ route('articles.show', $article) }}" tabindex="-1" aria-hidden="true">
            @php $srcset = \App\Support\ArticleImage::srcset($article->image); @endphp
            <img src="{{ $srcset ? asset('storage/'.\App\Support\ArticleImage::thumbPath($article->image)) : asset('storage/'.$article->image) }}"
                 @if ($srcset) srcset="{{ $srcset }}" sizes="(min-width: 1024px) 33vw, (min-width: 640px) 50vw, 100vw" @endif
                 alt="" width="1000" height="600" decoding="async"
                 @if ($eager) fetchpriority="high" @else loading="lazy" @endif class="h-64 w-full object-cover">
        </a>
    @endif

    <div class="flex flex-1 flex-col gap-3 p-5">
        <div>
            <a href="{{ route('articles.show', $article) }}" class="article-title silkscreen-bold text-2xl font-semibold transition hover:text-blue-600">{{ $article->title }}</a>
            <p class="byauthor silkscreen-regular mt-2 text-sm text-gray-600">
                by <a href="{{ route('articles.index', ['search' => '@'.$article->author->name]) }}" class="font-bold hover:underline">{{ $article->author->name }}</a>
                | in <a href="{{ route('articles.index', ['category' => $article->category_id]) }}" class="font-bold hover:underline">{{ $article->category->name }}</a>
            </p>
            @if ($article->reactions_count > 10)
                <span class="mt-2 inline-block rounded-full bg-yellow-300 px-2 py-1 text-xs font-semibold text-yellow-800">Popular</span>
            @endif
        </div>

        <div class="mt-auto flex flex-wrap items-center justify-between gap-3">
            <div class="flex flex-wrap items-center gap-3 text-sm text-gray-600">
                <form method="POST" action="{{ route('articles.like', $article) }}">
                    @csrf
                    <button type="submit" class="rounded-full bg-white px-3 py-1 font-semibold text-blue-700 shadow-sm transition hover:bg-blue-100">👍 Like</button>
                </form>
                <span>{{ $article->reactions_count }} {{ Str::plural('Like', $article->reactions_count) }}</span>
                <span>{{ $article->comments_count }} 💬</span>
                <span>{{ $article->created_at->diffForHumans() }}</span>
            </div>

            @if ($article->isWrittenBy(auth()->user()))
                <div class="ml-auto flex items-center gap-1">
                    <a href="{{ route('articles.edit', $article) }}" class="rounded-full p-2 text-lg transition hover:bg-yellow-100" aria-label="Edit this article">✏️</a>
                    <form method="POST" action="{{ route('articles.destroy', $article) }}" onsubmit="return confirm('Are you sure you want to delete this article?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="rounded-full p-2 text-lg transition hover:bg-red-100" aria-label="Delete this article">🗑️</button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</article>
