<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArticleCommentController extends Controller
{
    public function __invoke(Request $request, Article $article): RedirectResponse
    {
        $data = $request->validate(['content' => 'required|string|max:1000']);

        $article->comments()->create($data + ['user_id' => Auth::id()]);

        return redirect()->route('articles.show', $article)->with('success', 'Comment added successfully!');
    }
}
