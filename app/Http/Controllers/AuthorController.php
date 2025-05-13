<?php

namespace App\Http\Controllers;

use App\Models\Author;

class AuthorController extends Controller
{
    public function show($id)
    {
        return Author::with('articles')->findOrFail($id);
    }
}
