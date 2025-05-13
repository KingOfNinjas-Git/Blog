<?php

namespace App\Http\Controllers;

use App\Models\Category;

class CategoryController extends Controller
{
    public function show($id)
    {
        return Category::with('articles')->findOrFail($id);
    }
}
