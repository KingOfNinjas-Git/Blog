<?php

namespace App\Http\Controllers;



use App\Models\Article;
use App\Models\Author;
use App\Models\Category;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only('search', 'category');
        $query = Article::query();

        // Filter by category if provided
        if (!empty($filters['category'])) {
            $query->where('category_id', $filters['category']);
        }

        // Search by title or author
        if (!empty($filters['search'])) {
            $search = $filters['search'];

            if (str_starts_with($search, '@')) {
                // Search by author name
                $authorName = substr($search, 1); // Remove '@'
                $query->whereHas('author', function ($q) use ($authorName) {
                    $q->where('name', 'like', '%' . $authorName . '%');
                });
            } else {
                // Search by title
                $query->where('title', 'like', '%' . $search . '%');
            }
        }

        $articles = $query->with(['author', 'category', 'reactions', 'comments'])->paginate(10);
        $categories = Category::all();
        $latestPost = Article::latest()->first(); // Fetch the latest post
        $trendingPost = Article::withCount('reactions')->orderBy('reactions_count', 'desc')->first(); // Fetch the post with the most likes

        return view('articles.index', compact('articles', 'categories', 'filters', 'latestPost', 'trendingPost'));
    }

    // Show the form to create a new article
    public function create()
    {
        $authors = Author::all();
        $categories = Category::all();

        return view('articles.create', compact('authors', 'categories'));
    }

    // Store a newly created article
    public function store(Request $request)
    {

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle image upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('images', 'public');
        }

        // Create the article
        Article::create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'category_id' => $validated['category_id'],
            'author_id' => Auth::id(),
            'image' => $imagePath,
        ]);

        return redirect()->route('articles.index')->with('success', 'Article created successfully!');
    }

    // Edit an existing article
    public function edit($id)
    {
        $article = Article::findOrFail($id);
        $categories = Category::all();

        return view('articles.edit', compact('article', 'categories'));
    }

    // Update an existing article
    public function update(Request $request, $id)
    {
        $article = Article::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            if ($article->image) {
                Storage::delete('public/' . $article->image);
            }
            $validated['image'] = $request->file('image')->store('images', 'public');
        }

        $article->update($validated);

        return redirect()->route('articles.index')->with('success', 'Article updated successfully!');
    }

    // Delete an article
    public function destroy($id)
    {
        $article = Article::findOrFail($id);

        if (\Illuminate\Support\Facades\Auth::user()->id !== $article->author_id) {
            abort(403, 'Unauthorized action.');
        }

        $article->delete();

        return redirect()->route('articles.index')->with('success', 'Article deleted successfully!');
    }

    public function testReactions()
    {
        $article = Article::with('reactions')->find(1);
        dd($article->reactions);
    }

    public function like(Article $article)
    {
        // Check if the user has already liked the article
        $user = \Illuminate\Support\Facades\Auth::user();
        if ($article->reactions()->where('user_id', $user->id)->exists()) {
            return redirect()->back()->with('message', 'You have already liked this article.');
        }

        // Add a like reaction
        $article->reactions()->create([
            'user_id' => $user->id,
            'type' => 'like',
        ]);

        return redirect()->back()->with('message', 'Article liked successfully!');
    }

    public function storeComment(Request $request, $id)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        Comment::create([
            'article_id' => $id,
            'user_id' => Auth::user()->id,
            'content' => $request->content,
        ]);

        return redirect()->route('articles.show', $id)->with('success', 'Comment added successfully!');
    }
}
