<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\CategoryController;


Route::get('/dashboard', function () {
    return redirect('/');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// Correct route for listing articles
Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');

// Route for creating an article
Route::get('/articles/create', [ArticleController::class, 'create'])->name('articles.create');

// Route for storing an article
Route::post('/articles', [ArticleController::class, 'store'])->name('articles.store');

// Route for viewing a specific category
Route::get('/categories/{id}', fn ($id) => redirect()->route('articles.index', ['category' => $id]));

// Route for viewing a specific author
Route::get('/authors/{id}', function ($id) {
    $author = \App\Models\Author::findOrFail($id);

    return redirect()->route('articles.index', ['search' => '@' . $author->name]);
});

// Frontend routes
Route::get('/', [ArticleController::class, 'index']);
Route::get('/articles/{id}', [FrontendController::class, 'show']);

Route::resource('articles', ArticleController::class);

Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');

Route::post('/articles/{article}/like', [ArticleController::class, 'like'])->name('articles.like');

Route::post('/articles/{id}/comments', [ArticleController::class, 'storeComment'])->name('articles.comments.store');