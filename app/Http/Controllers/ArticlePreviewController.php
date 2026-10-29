<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/** Shows a writer how their text will look (with the formatting applied) before they save it. */
class ArticlePreviewController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $data = $request->validate(['content' => 'nullable|string|max:60000']);

        return response()->json(['html' => Article::make(['content' => $data['content'] ?? ''])->html()]);
    }
}
