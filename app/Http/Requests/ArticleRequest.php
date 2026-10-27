<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/** The checks for writing and editing an article. */
class ArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // who may do what is decided by the routes (signed in) and the controller (own articles only)
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
        ];
    }
}
