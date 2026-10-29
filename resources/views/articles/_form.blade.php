{{-- The form for writing a new article and for editing one: $article is set only when editing. --}}
@php($article ??= null)

<form action="{{ $article ? route('articles.update', $article) : route('articles.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6 text-left">
    @csrf
    @if ($article) @method('PUT') @endif

    @if ($errors->any())
        <ul role="alert" class="list-inside list-disc rounded-2xl bg-danger-soft p-4 text-sm font-medium text-danger">
            @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    @endif

    <div>
        <label for="title" class="mb-1.5 block text-sm font-bold">Title</label>
        <input id="title" type="text" name="title" value="{{ old('title', $article?->title) }}" required maxlength="255" placeholder="A headline people want to click" class="field !text-lg !font-semibold">
    </div>

    <div>
        <label for="content" class="mb-1.5 block text-sm font-bold">Content</label>
        <textarea id="content" name="content" rows="12" required placeholder="Write your story. A blank line starts a new paragraph." data-count="#content-stats" data-count-words class="field !font-[var(--font-read)] !text-lg !leading-8">{{ old('content', $article?->content) }}</textarea>
        <p class="mt-1.5 text-xs text-ink-soft"><span id="content-stats">0 words</span></p>
    </div>

    <div class="grid gap-6 sm:grid-cols-2">
        <div>
            <label for="category_id" class="mb-1.5 block text-sm font-bold">Category</label>
            <select id="category_id" name="category_id" required class="field">
                <option value="">Select a category</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(old('category_id', $article?->category_id) == $category->id)>{{ ucfirst(strtolower($category->name)) }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="image" class="mb-1.5 block text-sm font-bold">Cover picture @if ($article)<span class="font-normal text-ink-soft">(leave empty to keep it)</span>@endif</label>
            <input id="image" type="file" name="image" accept="image/*" data-preview="#image-preview" class="field !p-2 file:mr-3 file:rounded-full file:border-0 file:bg-brand-soft file:px-4 file:py-1.5 file:text-sm file:font-semibold file:text-brand">
        </div>
    </div>

    <figure class="{{ $article?->image ? '' : 'hidden' }} overflow-hidden rounded-2xl border border-line" id="image-figure">
        <img id="image-preview" src="{{ $article?->image ? asset('storage/'.$article->image) : '' }}" alt="Preview of the cover picture" class="max-h-72 w-full object-cover">
    </figure>

    <div class="flex flex-col gap-3 pt-2 sm:flex-row">
        <button type="submit" class="btn btn-primary !py-3 sm:flex-1">{{ $article ? 'Save Changes' : 'Create Article' }}</button>
        <a href="{{ $article ? route('articles.show', $article) : route('articles.index') }}" class="btn btn-ghost !py-3 sm:flex-1">Cancel</a>
    </div>
</form>
