<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

/** A signed-in user can like an article once. */
class ArticleLikeController extends Controller
{
    public function __invoke(Article $article): RedirectResponse
    {
        $like = ['user_id' => Auth::id(), 'type' => 'like'];

        if ($article->reactions()->where($like)->exists()) {
            return back()->with('message', 'You have already liked this article.');
        }

        $article->reactions()->create($like);

        return back()->with('message', 'Article liked successfully!');
    }
}
