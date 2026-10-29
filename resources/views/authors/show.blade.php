@extends('layouts.design')

@section('title', $author->name.' | My Blog')

@section('content')
    @php($own = auth()->id() === $author->id)

    <section class="relative overflow-hidden border-b border-line">
        <div class="pixel-bg pointer-events-none absolute inset-0" aria-hidden="true"></div>
        <div class="relative mx-auto flex max-w-6xl flex-col gap-8 px-4 py-12 sm:flex-row sm:items-center sm:py-16">
            <x-avatar :name="$author->name" size="lg" class="!h-24 !w-24 !text-4xl" />
            <div class="flex-1">
                <p class="kicker">{{ $own ? 'Your page' : 'Writer' }}</p>
                <h1 class="mt-2 text-4xl font-extrabold tracking-tight sm:text-5xl">{{ $author->name }}</h1>
                <p class="mt-2 text-ink-soft">Writing here since {{ $author->created_at->format('F Y') }}</p>
                @if ($own)
                    <a href="{{ route('articles.create') }}" class="btn btn-primary mt-5">Write a new article</a>
                @endif
            </div>
            <dl class="grid grid-cols-3 gap-3 sm:w-80" aria-label="{{ $author->name }} in numbers">
                @foreach ([['articles', 'Articles'], ['likes', 'Likes'], ['comments', 'Comments']] as [$key, $label])
                    <div class="panel !rounded-2xl px-3 py-3 text-center">
                        <dd class="pixel-title text-lg text-brand">{{ number_format($stats[$key]) }}</dd>
                        <dt class="mt-1 text-xs font-semibold text-ink-soft">{{ $label }}</dt>
                    </div>
                @endforeach
            </dl>
        </div>
    </section>

    <div class="mx-auto max-w-6xl px-4 py-10">
        @if ($drafts->isNotEmpty())
            <section class="mb-12" aria-labelledby="drafts-title">
                <h2 id="drafts-title" class="kicker mb-4">Your drafts <span class="normal-case tracking-normal text-ink-soft">(only you can see these)</span></h2>
                <ul class="grid gap-3 md:grid-cols-2">
                    @foreach ($drafts as $draft)
                        <li class="panel flex items-center gap-4 !rounded-2xl p-4">
                            <span class="badge !bg-accent !text-on-accent">Draft</span>
                            <div class="min-w-0 flex-1">
                                <a href="{{ route('articles.show', $draft) }}" class="block truncate font-bold hover:text-brand">{{ $draft->title }}</a>
                                <p class="text-xs text-ink-soft">{{ $draft->category->name }} &middot; saved {{ $draft->updated_at->diffForHumans() }}</p>
                            </div>
                            <x-owner-actions :article="$draft" />
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif

        <section aria-labelledby="author-articles-title">
            <h2 id="author-articles-title" class="kicker mb-5">{{ $own ? 'Your articles' : 'Articles by '.$author->name }}</h2>

            @if ($articles->isEmpty())
                <div class="panel p-10 text-center">
                    <p class="text-lg font-semibold">No published articles yet.</p>
                    @if ($own)
                        <p class="mt-1 text-ink-soft">Write your first one, or publish a draft.</p>
                        <a href="{{ route('articles.create') }}" class="btn btn-primary mt-6">Write an article</a>
                    @endif
                </div>
            @else
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($articles as $article)
                        <x-article-card :article="$article" :liked="in_array($article->id, $likedIds, true)" />
                    @endforeach
                </div>
                <x-pager :paginator="$articles" />
            @endif
        </section>
    </div>
@endsection
