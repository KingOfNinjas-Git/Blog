{{-- The form for writing a new article and for editing one: $article is set only when editing. --}}
@php
    $article ??= null;
    $input = 'mt-1 block w-full rounded-xl border border-gray-300 p-3 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500';
@endphp

<form action="{{ $article ? route('articles.update', $article) : route('articles.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6 text-left">
    @csrf
    @if ($article) @method('PUT') @endif

    @if ($errors->any())
        <ul role="alert" class="list-inside list-disc rounded-xl bg-red-50 p-4 text-sm text-red-600">
            @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    @endif

    <div>
        <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
        <input id="title" type="text" name="title" value="{{ old('title', $article?->title) }}" required maxlength="255" class="{{ $input }}">
    </div>

    <div>
        <label for="content" class="block text-sm font-medium text-gray-700">Content</label>
        <textarea id="content" name="content" rows="8" required class="{{ $input }}">{{ old('content', $article?->content) }}</textarea>
    </div>

    <div>
        <label for="category_id" class="block text-sm font-medium text-gray-700">Category</label>
        <select id="category_id" name="category_id" required class="{{ $input }}">
            <option value="">Select a category</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected(old('category_id', $article?->category_id) == $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label for="image" class="block text-sm font-medium text-gray-700">Image @if ($article) (leave empty to keep the current one) @endif</label>
        @if ($article?->image)
            <img src="{{ asset('storage/'.$article->image) }}" alt="Current picture" width="128" height="128" loading="lazy" class="mt-2 h-32 w-32 rounded-xl object-cover shadow">
        @endif
        <input id="image" type="file" name="image" accept="image/*" class="{{ $input }}">
    </div>

    <div class="flex flex-col gap-3 sm:flex-row">
        <button type="submit" class="w-full rounded-full bg-blue-600 px-6 py-3 font-semibold text-white shadow transition hover:bg-blue-700">
            {{ $article ? 'Save Changes' : 'Create Article' }}
        </button>
        <a href="{{ $article ? route('articles.show', $article) : route('articles.index') }}" class="w-full rounded-full border border-gray-300 px-6 py-3 text-center font-semibold text-gray-700 transition hover:bg-gray-50">Cancel</a>
    </div>
</form>
