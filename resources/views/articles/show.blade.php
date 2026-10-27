@extends('layouts.design')

@section('title', $article->title.' | My Blog')

@section('content')
    <article class="mx-auto max-w-3xl rounded-3xl border border-blue-300 bg-white p-6 text-left shadow-2xl shadow-blue-950/20 md:p-8">
        <h1 class="silkscreen-bold mb-4 text-3xl font-bold text-gray-800">{{ $article->title }}</h1>

        <p class="silkscreen-regular mb-6 text-sm text-gray-600">
            <strong>By:</strong> <a href="{{ route('articles.index', ['search' => '@'.$article->author->name]) }}" class="hover:underline">{{ $article->author->name }}</a><br>
            <strong>Category:</strong> <a href="{{ route('articles.index', ['category' => $article->category_id]) }}" class="hover:underline">{{ $article->category->name }}</a>
        </p>

        @if ($article->image)
            @php($size = $article->imageSize())
            <img src="{{ asset('storage/'.$article->image) }}" alt="{{ $article->title }}" fetchpriority="high" decoding="async"
                 @if ($size) width="{{ $size[0] }}" height="{{ $size[1] }}" @endif class="mb-6 h-auto w-full rounded-2xl shadow-md">
        @endif

        <div class="silkscreen-regular mb-6 leading-relaxed text-gray-700">{!! nl2br(e($article->content)) !!}</div>

        @if ($article->isWrittenBy(auth()->user()))
            <div class="mb-6 flex gap-3">
                <a href="{{ route('articles.edit', $article) }}" class="rounded-full bg-yellow-400 px-5 py-2 font-semibold text-yellow-950 shadow transition hover:bg-yellow-300">✏️ Edit</a>
                <form method="POST" action="{{ route('articles.destroy', $article) }}" onsubmit="return confirm('Are you sure you want to delete this article?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="rounded-full bg-red-500 px-5 py-2 font-semibold text-white shadow transition hover:bg-red-600">🗑️ Delete</button>
                </form>
            </div>
        @endif

        <section class="mt-12" aria-labelledby="comments-title">
            <h2 id="comments-title" class="silkscreen-bold mb-8 border-b-4 border-blue-500 pb-2 text-center text-3xl font-bold text-gray-800">Comments</h2>

            @forelse ($article->comments as $comment)
                <div class="mb-6 rounded-2xl border border-gray-200 bg-white p-6 shadow-md">
                    <div class="mb-3 flex items-center">
                        <span class="silkscreen-regular text-lg font-semibold text-blue-700">{{ $comment->user->name }}</span>
                        <span class="ml-auto text-sm text-gray-600">{{ $comment->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="silkscreen-regular leading-relaxed text-gray-700">{{ $comment->content }}</p>
                </div>
            @empty
                <p class="text-center text-lg text-gray-600">No comments yet. Be the first to comment!</p>
            @endforelse

            @auth
                <form action="{{ route('articles.comments.store', $article) }}" method="POST" class="mt-10 rounded-2xl bg-blue-50 p-6 shadow-lg md:p-8">
                    @csrf
                    <h3 class="mb-6 text-center text-2xl font-semibold text-blue-700">Leave a Comment</h3>
                    <label for="content" class="sr-only">Your comment</label>
                    <textarea name="content" id="content" rows="5" required maxlength="1000" placeholder="Write your comment here..."
                              class="mb-2 w-full rounded-xl border border-gray-300 p-4 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('content') }}</textarea>
                    @error('content')<p class="mb-4 text-sm text-red-600">{{ $message }}</p>@enderror
                    <div class="mt-4 flex justify-center">
                        <button type="submit" class="rounded-full bg-blue-600 px-8 py-3 font-semibold text-white shadow transition hover:bg-blue-700">Submit Comment</button>
                    </div>
                </form>
            @else
                <p class="mt-6 text-center text-lg text-gray-600">
                    You must <a href="{{ route('login') }}" class="text-blue-700 hover:underline">log in</a> to leave a comment.
                </p>
            @endauth
        </section>

        <p class="mt-8"><a href="{{ route('articles.index') }}" class="text-lg text-blue-700 hover:underline">&larr; Back to articles</a></p>
    </article>
@endsection
