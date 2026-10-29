<?php

use App\Http\Controllers\ArticleCommentController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\ArticleLikeController;
use App\Http\Controllers\ArticlePreviewController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\CommentDeleteController;
use App\Http\Controllers\ProfileController;
use App\Models\Category;
use Illuminate\Support\Facades\Route;

// Reading: the home page is the list of articles
Route::get('/', [ArticleController::class, 'index']);
Route::get('/categories/{category}', fn (Category $category) => redirect()->route('articles.index', ['category' => $category->id]));
Route::get('/authors/{author}', [AuthorController::class, 'show'])->name('authors.show');

// Writing, liking, commenting and the profile need an account. (These come before the "articles/{article}" page
// below so that /articles/create is not taken for an article.)
Route::middleware('auth')->group(function () {
    Route::post('/articles/preview', ArticlePreviewController::class)->name('articles.preview');
    Route::resource('articles', ArticleController::class)->except(['index', 'show']);
    Route::post('/articles/{article}/like', ArticleLikeController::class)->name('articles.like');
    Route::post('/articles/{article}/comments', ArticleCommentController::class)->name('articles.comments.store');
    Route::delete('/comments/{comment}', CommentDeleteController::class)->name('comments.destroy');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::resource('articles', ArticleController::class)->only(['index', 'show']);

// Breeze's pages after signing up or verifying an e-mail lead here
Route::redirect('/dashboard', '/')->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/auth.php';
