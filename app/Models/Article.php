<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Article extends Model
{
    use HasFactory;

    // Add the $fillable property here
    protected $fillable = ['title', 'content', 'author_id', 'category_id', 'image'];

    /** The picture's width and height, read from the file, so a page can reserve the space before it loads. */
    public function imageSize(): ?array
    {
        if (! $this->image) {
            return null;
        }

        $info = @getimagesize(Storage::disk('public')->path($this->image));

        return $info ? [$info[0], $info[1]] : null;
    }

    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function reactions()
    {
        return $this->hasMany(Reaction::class);
    }
}
