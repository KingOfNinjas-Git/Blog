<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\Category;
use App\Models\Author;

class FrontendController extends Controller
{
    public function index(Request $request)
    {
        $query = Article::with(['author', 'category']);

        if ($request->filled('author')) {
            $query->where('author_id', $request->author);
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        return view('articles.index', [
            'articles' => $query->paginate(5),
            'categories' => Category::all(),
            'authors' => Author::all(),
            'filters' => $request->only(['author', 'category', 'search']),
        ]);
    }

    public function show($id)
    {
        $article = Article::with(['author', 'category'])->findOrFail($id);
        return view('articles.show', compact('article'));
    }
}
