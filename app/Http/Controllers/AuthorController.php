<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Comment;
use App\Models\Reaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/** A writer's page: their published articles and what readers made of them (and, for the writer only, their drafts). */
class AuthorController extends Controller
{
    public function show(Author $author): View
    {
        $published = $author->articles()->published();
        $ids = (clone $published)->pluck('id');

        $articles = (clone $published)->with(['author', 'category'])->withCount(['reactions', 'comments'])
            ->orderedBy('newest')->paginate(9);

        // Only the writer sees their own drafts, and only on their own page
        $drafts = Auth::id() === $author->id
            ? $author->articles()->where('status', 'draft')->with('category')->latest()->get()
            : collect();

        return view('authors.show', [
            'author' => $author,
            'articles' => $articles,
            'drafts' => $drafts,
            'likedIds' => Auth::check() ? Reaction::where('user_id', Auth::id())->pluck('article_id')->all() : [],
            'stats' => [
                'articles' => $ids->count(),
                'likes' => Reaction::whereIn('article_id', $ids)->count(),
                'comments' => Comment::whereIn('article_id', $ids)->count(),
            ],
        ]);
    }
}
