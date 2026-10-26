<!-- filepath: c:\Users\franc\Herd\Blog\resources\views\articles\edit.blade.php -->
@extends('layouts.design')

@section('content')
    <div class="min-h-screen flex items-center justify-center">
        <div class="container mx-auto p-8 bg-white rounded-lg shadow-lg border border-blue-400">
            <h1 class="text-3xl font-bold text-cyan-600 mb-6 press-start-2p-regular">Edit Article</h1>

            <form method="POST" action="{{ route('articles.update', $article->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Title -->
                <div class="mb-4">
                    <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                    <input type="text" name="title" id="title" value="{{ old('title', $article->title) }}"
                        class="w-full p-3 text-sm border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('title')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Content -->
                <div class="mb-4">
                    <label for="content" class="block text-sm font-medium text-gray-700">Content</label>
                    <textarea name="content" id="content" rows="6"
                        class="w-full p-3 text-sm border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('content', $article->content) }}</textarea>
                    @error('content')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Category -->
                <div class="mb-4">
                    <label for="category_id" class="block text-sm font-medium text-gray-700">Category</label>
                    <select name="category_id" id="category_id"
                        class="w-full p-3 text-sm border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ $article->category_id == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Image -->
                <div class="mb-4">
                    <label for="image" class="block text-sm font-medium text-gray-700">Image</label>
                    <input type="file" name="image" id="image"
                        class="w-full p-3 text-sm border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @if ($article->image)
                        <p class="mt-2">Current Image:</p>
                        <img src="{{ asset('storage/' . $article->image) }}" alt="{{ $article->title }}"
                            width="128" height="128" loading="lazy" class="w-32 h-32 object-cover rounded-lg mt-2">
                    @endif
                    @error('image')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="mt-6">
                    <button type="submit"
                        class="bg-blue-600 text-white font-semibold py-3 px-6 rounded-lg shadow hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm transition">
                        Update Article
                    </button>
                    <a href="{{ route('articles.index') }}"
                        class="ml-4 text-blue-700 hover:underline text-sm">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection