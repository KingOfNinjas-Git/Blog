<?php

namespace App\Http\Controllers;

use App\Http\Requests\ArticleRequest;
use App\Models\Article;
use App\Models\Author;
use App\Models\Category;
use App\Models\Reaction;
use App\Support\ArticleImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/** Reading is open to everyone; writing, editing and deleting need a signed-in user, and only on their own articles. */
class ArticleController extends Controller
{
    private const SORTS = ['newest', 'popular', 'discussed'];

    public function index(Request $request): View
    {
        $filters = $request->only('search', 'category');
        $sort = in_array($request->query('sort'), self::SORTS, true) ? $request->query('sort') : 'newest';

        $articles = Article::with(['author', 'category'])
            ->withCount(['reactions', 'comments'])
            ->filter($filters)
            ->orderedBy($sort)
            ->paginate(10)
            ->withQueryString();

        // On the plain first page the newest article gets the big card and the most liked ones are shown as "trending"
        $plain = $articles->currentPage() === 1 && $sort === 'newest' && empty($filters['search']) && empty($filters['category']);
        $featured = $plain && $articles->count() > 1 ? $articles->first() : null;
        $items = $featured ? $articles->getCollection()->slice(1)->values() : $articles->getCollection();
        $trending = $plain
            ? Article::with(['author', 'category'])->withCount('reactions')->has('reactions')
                ->orderByDesc('reactions_count')->orderByDesc('created_at')->limit(3)->get()
            : collect();

        return view('articles.index', [
            'articles' => $articles,
            'items' => $items,
            'featured' => $featured,
            'trending' => $trending,
            'categories' => Category::withCount('articles')->get(),
            'filters' => $filters,
            'sort' => $sort,
            'stats' => ['articles' => Article::count(), 'writers' => Author::count(), 'likes' => Reaction::count()],
            'likedIds' => $this->likedIds(),
        ]);
    }

    public function show(Article $article): View
    {
        $article->load(['author', 'category', 'comments.user'])->loadCount(['reactions', 'comments']);

        // Up to three more, the ones from the same category first
        $related = Article::with(['author', 'category'])->withCount(['reactions', 'comments'])
            ->whereKeyNot($article->id)->orderByRaw('category_id = ? desc', [$article->category_id])->latest()->limit(3)->get();

        return view('articles.show', [
            'article' => $article,
            'related' => $related,
            'liked' => in_array($article->id, $this->likedIds(), true),
        ]);
    }

    /** The ids of the articles the signed-in visitor has liked (none for a guest). */
    private function likedIds(): array
    {
        return Auth::check() ? Reaction::where('user_id', Auth::id())->pluck('article_id')->all() : [];
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
