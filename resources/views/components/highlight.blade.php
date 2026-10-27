@props(['title', 'article' => null, 'colors', 'empty', 'likes' => false])
{{-- A coloured box in the sidebar ("Latest Post", "Trending Post") with one article. --}}
<section {{ $attributes->class(['rounded-2xl bg-gradient-to-r p-6 text-white shadow-lg', $colors]) }}>
    <h2 class="press-start-2p-regular mb-4 text-xl font-bold">{{ $title }}</h2>

    @if ($article)
        <div class="silkscreen-bold flex flex-col items-center">
            <a href="{{ route('articles.show', $article) }}" class="text-lg font-semibold underline-offset-4 hover:underline">{{ $article->title }}</a>
            <p class="mt-1 text-sm text-white/80">by <strong>{{ $article->author->name }}</strong> | {{ $article->created_at->diffForHumans() }}</p>
            @if ($article->image)
                <img src="{{ asset('storage/'.$article->image) }}" alt="" width="180" height="128" loading="lazy" decoding="async"
                     class="mt-4 h-32 w-44 rounded-xl object-cover shadow-md">
            @endif
            @if ($likes)
                <p class="mt-2 text-sm text-white/80">{{ $article->reactions_count }} {{ Str::plural('Like', $article->reactions_count) }}</p>
            @endif
        </div>
    @else
        <p>{{ $empty }}</p>
    @endif
</section>
