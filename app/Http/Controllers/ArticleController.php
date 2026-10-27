<?php

namespace App\Http\Controllers;

use App\Http\Requests\ArticleRequest;
use App\Models\Article;
use App\Models\Category;
use App\Support\ArticleImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/** Reading is open to everyone; writing, editing and deleting need a signed-in user, and only on their own articles. */
class ArticleController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->only('search', 'category');

        return view('articles.index', [
            'articles' => Article::with(['author', 'category'])
                ->withCount(['reactions', 'comments'])
                ->filter($filters)
                ->orderBy('created_at')->orderBy('id')
                ->paginate(10)
                ->withQueryString(),
            'categories' => Category::all(),
            'filters' => $filters,
            'latestPost' => Article::with('author')->latest()->first(),
            'trendingPost' => Article::with('author')->withCount('reactions')->orderByDesc('reactions_count')->first(),
        ]);
    }

    public function show(Article $article): View
    {
        return view('articles.show', ['article' => $article->load(['author', 'category', 'comments.user'])]);
    }

    public function create(): View
    {
        return view('articles.create', ['categories' => Category::all()]);
    }

    public function store(ArticleRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['author_id'] = Auth::id();
        $data['image'] = $request->hasFile('image') ? ArticleImage::store($request->file('image')) : null;

        Article::create($data);

        return redirect()->route('articles.index')->with('success', 'Article created successfully!');
    }

    public function edit(Article $article): View
    {
        $this->mustBeTheAuthor($article);

        return view('articles.edit', ['article' => $article, 'categories' => Category::all()]);
    }

    public function update(ArticleRequest $request, Article $article): RedirectResponse
    {
        $this->mustBeTheAuthor($article);

        $data = $request->validated();

        if ($request->hasFile('image')) {
            ArticleImage::delete($article->image);
            $data['image'] = ArticleImage::store($request->file('image'));
        } else {
            unset($data['image']);
        }

        $article->update($data);

        return redirect()->route('articles.index')->with('success', 'Article updated successfully!');
    }

    public function destroy(Article $article): RedirectResponse
    {
        $this->mustBeTheAuthor($article);

        ArticleImage::delete($article->image);
        $article->delete();

        return redirect()->route('articles.index')->with('success', 'Article deleted successfully!');
    }

    private function mustBeTheAuthor(Article $article): void
    {
        abort_unless($article->isWrittenBy(Auth::user()), 403, 'You can only change your own articles.');
    }
}
