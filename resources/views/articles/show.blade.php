@extends('layouts.design')

@section('content')
<div class="max-w-3xl mx-auto bg-white p-8 rounded-lg shadow-lg border border-blue-400">
    <h1 class="text-3xl font-bold text-gray-800 mb-4 silkscreen-bold">{{ $article->title }}</h1>

    <div class="mb-4">
        <p class="text-sm text-gray-600 silkscreen-regular">
            <strong>By:</strong> {{ $article->author->name }}
        </p>
        <p class="text-sm text-gray-600 silkscreen-regular">
            <strong>Category:</strong> {{ $article->category->name }}
        </p>
    </div>

    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-2 silkscreen-bold">Content</h2>
        <div class="text-gray-700 leading-relaxed silkscreen-regular">
            {!! nl2br(e($article->content)) !!}
        </div>
    </div>

    @if ($article->image)
        <div class="mb-4">
            <img src="{{ asset('storage/' . $article->image) }}" alt="Article Image" class="rounded-lg shadow-md">
        </div>
    @endif

   <!-- Comments Section -->
    <div class="mt-12">
        <h2 class="text-3xl font-bold text-gray-800 mb-8 silkscreen-bold text-center border-b-4 border-blue-500 pb-2">Comments</h2>

        <!-- Display Comments -->
        @forelse ($article->comments as $comment)
            <div class="mb-6 p-6 bg-white border border-gray-200 rounded-lg shadow-md">
                <div class="flex items-center mb-4">
                    <div class="text-blue-600 font-semibold silkscreen-regular text-lg">{{ $comment->user->name }}</div>
                    <div class="ml-auto text-sm text-gray-500">{{ $comment->created_at->diffForHumans() }}</div>
                </div>
                <p class="text-gray-700 silkscreen-regular text-left leading-relaxed">{{ $comment->content }}</p>
            </div>
        @empty
            <p class="text-gray-600 text-center text-lg">No comments yet. Be the first to comment!</p>
        @endforelse

        <!-- Add Comment Form -->
        @auth
            <form action="{{ route('articles.comments.store', $article->id) }}" method="POST" class="mt-10 bg-blue-50 p-8 rounded-lg shadow-lg">
                @csrf
                <h3 class="text-2xl font-semibold mb-6 text-blue-700 text-center">Leave a Comment</h3>
                <div class="mb-6">
                    <textarea name="content" id="content" rows="5"
                        class="w-full p-4 text-sm border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Write your comment here..." required></textarea>
                </div>
                <div class="flex justify-center">
                    <button type="submit"
                        class="bg-blue-600 text-white font-semibold py-3 px-8 rounded-lg shadow hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                        Submit Comment
                    </button>
                </div>
            </form>
        @else
            <p class="text-gray-600 text-center mt-6 text-lg">
                You must <a href="{{ route('login') }}" class="text-blue-500 hover:underline">log in</a> to leave a comment.
            </p>
        @endauth
    </div>

    <div class="mt-8">
        <a href="{{ url('/articles') }}" class="text-blue-500 hover:underline text-lg">&larr; Back to articles</a>
    </div>
</div>
@endsection