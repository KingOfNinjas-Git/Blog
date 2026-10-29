<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

/** A comment can be removed by the person who wrote it, or by the writer of the article it is under. */
class CommentDeleteController extends Controller
{
    public function __invoke(Comment $comment): RedirectResponse
    {
        $comment->loadMissing('article');

        abort_unless(
            $comment->user_id === Auth::id() || $comment->article->isWrittenBy(Auth::user()),
            403,
            'You can only remove your own comments, or comments on your own articles.',
        );

        $comment->delete();

        return back()->with('success', 'Comment removed.');
    }
}
