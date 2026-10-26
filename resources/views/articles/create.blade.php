@extends('layouts.design')

@section('content')
<div class="max-w-2xl mx-auto bg-white p-8 rounded-lg shadow-lg">
    <h1 class="text-2xl font-bold mb-6 text-gray-800">Create New Article</h1>

    <!-- Display errors -->
    @if ($errors->any())
        <div class="mb-4">
            <ul class="list-disc list-inside text-red-500">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form -->
    <form action="{{ route('articles.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
    
        <!-- Title -->
        <div>
            <label class="block text-sm font-medium text-gray-700">Title</label>
            <input type="text" name="title" class="mt-1 block w-full rounded-lg border border-gray-300 p-3 shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
        </div>
    
        <!-- Content -->
        <div>
            <label class="block text-sm font-medium text-gray-700">Content</label>
            <textarea name="content" rows="5" class="mt-1 block w-full rounded-lg border border-gray-300 p-3 shadow-sm focus:ring-blue-500 focus:border-blue-500" required></textarea>
        </div>
    
        <!-- Category -->
        <div>
            <label class="block text-sm font-medium text-gray-700">Category</label>
            <select name="category_id" class="mt-1 block w-full rounded-lg border border-gray-300 p-3 shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                <option value="">Select a category</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
    
        <!-- Image -->
        <div>
            <label class="block text-sm font-medium text-gray-700">Image</label>
            <input type="file" name="image" accept="image/*"
                class="mt-1 block w-full rounded-lg border border-gray-300 p-3 shadow-sm focus:ring-blue-500 focus:border-blue-500">
        </div>
    
        <!-- Submit Button -->
        <div>
            <button type="submit" class="w-full bg-blue-500 text-white font-semibold py-2 px-4 rounded-lg shadow hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
                Create Article
            </button>
        </div>
    </form>
</div>
@endsection