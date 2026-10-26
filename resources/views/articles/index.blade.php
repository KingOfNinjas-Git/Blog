<!-- filepath: c:\Users\franc\Herd\Blog\resources\views\articles\index.blade.php -->
@extends('layouts.design')

@section('content')
    <div class="min-h-screen">
        <!-- Navbar de categorias -->
        <nav class="bg-gradient-to-r from-blue-500 to-cyan-500 text-white py-4 shadow-lg  ">
            <div class="container mx-auto flex space-x-4 overflow-x-auto justify-center items-center pixelify-sans-category">
                <a href="{{ route('articles.index') }}" 
                   class="px-6 py-2 rounded-full bg-white text-blue-700 font-semibold shadow-md hover:bg-blue-600 hover:text-white transition-all duration-300">
                    ALL CATEGORIES
                </a>
                @foreach ($categories as $category)
                    <a href="{{ route('articles.index', ['category' => $category->id]) }}" 
                       class="px-6 py-2 rounded-full bg-white text-blue-700 font-semibold shadow-md hover:bg-blue-600 hover:text-white transition-all duration-300">
                        {{ $category->name }}
                    </a>
                @endforeach
            </div>
        </nav>

        <div class="container mx-auto p-8 bg-white rounded-lg shadow-lg border border-blue-400">
            <!-- Flexbox Layout -->
            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Main Content -->
                <div class="flex-grow">
                    <!-- Header Section -->
                    <div class="flex justify-between items-center mb-8">
                        <h1 class="text-4xl font-extrabold text-cyan-600 press-start-2p-regular">ARTICLES</h1>
                        <div class="flex items-center space-x-4">
                            @if (Auth::check())
                                <span class="text-[#0486A2]">Welcome, <strong>{{ Auth::user()->name }}</strong>!</span>
                                <a href="{{ route('articles.create') }}"
                                    class="bg-[#037A91] text-white px-4 py-2 rounded-lg shadow hover:bg-[#0491AA] transition">
                                    New Article
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                        class="bg-[#036E86] text-white px-4 py-2 rounded-lg shadow hover:bg-[#04809D] transition">
                                        Logout
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('login') }}"
                                    class="bg-[#037A91] text-white px-4 py-2 rounded-lg shadow hover:bg-[#0491AA] transition">
                                    Login
                                </a>
                                <a href="{{ route('register') }}"
                                    class="bg-[#036E86] text-white px-4 py-2 rounded-lg shadow hover:bg-[#04809D] transition">
                                    Create Account
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Filter Form -->
                    <form method="GET" class="flex items-center gap-2 mb-8 justify-start">
                        <div class="flex-grow">
                            <input type="text" name="search" placeholder="Search title or @author..."
                                value="{{ $filters['search'] ?? '' }}"
                                class="w-full p-3 text-sm border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <button type="submit"
                                class="bg-blue-600 text-white font-semibold py-3 px-6 rounded-lg shadow hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm transition">
                                Filter
                            </button>
                        </div>
                    </form>

                    <!-- Article List -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach ($articles as $article)
                            <div class="border border-blue-700 rounded-lg p-6 shadow-sm hover:shadow-md transition-shadow bg-blue-50">
                                <div class="flex flex-col space-y-4">
                                    @if (!empty($article->image))
                                        <img src="{{ asset('storage/' . $article->image) }}" alt="{{ $article->title }}"
                                            width="1000" height="600" decoding="async" @if($loop->index >= 2) loading="lazy" @endif
                                            class="w-full h-80 object-cover rounded-lg">
                                    @endif

                                    <div>
                                        <a href="{{ url('/articles/' . $article->id) }}"
                                            class="text-2xl font-semibold hover:text-blue-600 transition silkscreen-bold article-title">
                                            {{ $article->title }}
                                        </a>
                                        <p class="text-sm text-gray-600 mt-2 silkscreen-regular byauthor">
                                            by <strong>{{ $article->author->name }}</strong> | in
                                            <strong>{{ $article->category->name }}</strong>
                                        </p>
                                        @if ($article->reactions->count() > 10)
                                            <span class="bg-yellow-300 text-yellow-800 text-xs font-semibold px-2 py-1 rounded-full">
                                                Popular
                                            </span>
                                        @endif
                                    </div>

                                    <div class="flex items-center justify-between mt-2">
                                        <!-- Like, Comments, and Date -->
                                        <div class="flex items-center space-x-4">
                                            <form method="POST" action="{{ route('articles.like', $article->id) }}">
                                                @csrf
                                                <button type="submit" class="text-blue-700 hover:text-blue-600 transition text-sm">
                                                    👍 Like
                                                </button>
                                            </form>
                                            <span class="text-gray-600 text-sm">{{ $article->reactions->count() }} Likes</span>
                                            <p class="text-sm text-gray-600">
                                                {{ $article->comments->count() }} 💬
                                            </p>
                                            <p class="text-sm text-gray-600">
                                                {{ $article->created_at->diffForHumans() }}
                                            </p>
                                        </div>

                                        <!-- Edit and Delete Buttons -->
                                        @if (Auth::check() && Auth::id() === $article->author_id)
                                            <div class="flex items-center space-x-2 ml-auto">
                                                <!-- Edit Button -->
                                                <a href="{{ route('articles.edit', $article->id) }}"
                                                    class="text-yellow-500 hover:text-yellow-600 transition text-lg">
                                                    ✏️
                                                </a>

                                                <!-- Delete Button -->
                                                <form method="POST" action="{{ route('articles.destroy', $article->id) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-500 hover:text-red-600 transition text-lg"
                                                        onclick="return confirm('Are you sure you want to delete this article?')">
                                                        🗑️
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <!-- Pagination -->
                    <div class="mt-8">
                        {{ $articles->links('pagination::tailwind') }}
                    </div>
                </div>
                <!-- Sidebar Section -->
                <div class="w-full lg:w-1/3 flex flex-col gap-6">
                    <!-- Latest Post Section -->
                    <div class="bg-gradient-to-r from-yellow-400 to-orange-500 text-white p-6 rounded-lg shadow-lg">
                        <h2 class="text-xl font-bold mb-4 press-start-2p-regular">Latest Post</h2>
                        @if ($latestPost)
                            <div class="flex flex-col items-center silkscreen-bold">
                                <a href="{{ url('/articles/' . $latestPost->id) }}" 
                                   class="text-lg font-semibold hover:text-blue-600">
                                    {{ $latestPost->title }}
                                </a>
                                <p class="text-sm text-gray-200 mt-1">
                                    by <strong>{{ $latestPost->author->name }}</strong> | 
                                    {{ $latestPost->created_at->diffForHumans() }}
                                </p>
                                @if (!empty($latestPost->image))
                                <img src="{{ asset('storage/' . $latestPost->image) }}" 
                                     alt="{{ $latestPost->title }}" 
                                     width="180" height="128" loading="lazy" decoding="async" class="w-45 h-32 object-cover rounded-lg mt-4">
                                @endif
                            </div>
                        @else
                            <p>No posts available.</p>
                        @endif
                    </div>

                    <!-- Trending Post Section -->
                    <div class="bg-gradient-to-r from-purple-400 to-pink-500 text-white p-6 rounded-lg shadow-lg">
                        <h2 class="text-xl font-bold mb-4 press-start-2p-regular">Trending Post</h2>
                        @if ($trendingPost)
                            <div class="flex flex-col items-center silkscreen-bold">
                                <a href="{{ url('/articles/' . $trendingPost->id) }}" 
                                   class="text-lg font-semibold hover:text-blue-600">
                                    {{ $trendingPost->title }}
                                </a>
                                <p class="text-sm text-gray-200 mt-1">
                                    by <strong>{{ $trendingPost->author->name }}</strong> | 
                                    {{ $trendingPost->created_at->diffForHumans() }}
                                </p>
                                @if (!empty($trendingPost->image))
                                <img src="{{ asset('storage/' . $trendingPost->image) }}" 
                                     alt="{{ $trendingPost->title }}" 
                                     width="180" height="128" loading="lazy" decoding="async" class="w-45 h-32 object-cover rounded-lg mt-4">
                                @endif
                                <p class="text-sm text-gray-200 mt-2">
                                    {{ $trendingPost->reactions->count() }} Likes
                                </p>
                            </div>
                        @else
                            <p>No trending posts available.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
