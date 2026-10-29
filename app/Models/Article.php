<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Article extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'content', 'author_id', 'category_id', 'image', 'image_alt'];

    /** The list page's filters: ?category=<id> and ?search=<words in the title>, or ?search=@<author name>. */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['category'] ?? null, fn (Builder $q, $category) => $q->where('category_id', $category))
            ->when($filters['search'] ?? null, function (Builder $q, string $search) {
                if (str_starts_with($search, '@')) {
                    return $q->whereHas('author', fn (Builder $a) => $a->where('name', 'like', '%'.substr($search, 1).'%'));
                }

                return $q->where('title', 'like', '%'.$search.'%');
            });
    }

    /** The list's order: ?sort=newest (the default), popular (most likes) or discussed (most comments). Needs withCount(['reactions', 'comments']). */
    public function scopeOrderedBy(Builder $query, ?string $sort): Builder
    {
        return match ($sort) {
            'popular' => $query->orderByDesc('reactions_count')->orderByDesc('created_at')->orderByDesc('id'),
            'discussed' => $query->orderByDesc('comments_count')->orderByDesc('created_at')->orderByDesc('id'),
            default => $query->orderByDesc('created_at')->orderByDesc('id'),
        };
    }

    /** About how many minutes the article takes to read (200 words a minute, at least one). */
    public function readingMinutes(): int
    {
        return max(1, (int) ceil(str_word_count(strip_tags($this->content)) / 200));
    }

    /** A short plain-text teaser for the cards. */
    public function excerpt(int $limit = 140): string
    {
        return Str::limit(trim(preg_replace('/\s+/', ' ', strip_tags($this->content))), $limit);
    }

    /** The text as paragraphs (a blank line starts a new one), so the page can show it as readable blocks. */
    public function paragraphs(): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/\R{2,}/', $this->content) ?: []), fn ($p) => $p !== ''));
    }

    /** The picture's width and height, read from the file, so a page can reserve the space before it loads. */
    public function imageSize(): ?array
    {
        if (! $this->image) {
            return null;
        }

        $info = @getimagesize(Storage::disk('public')->path($this->image));

        return $info ? [$info[0], $info[1]] : null;
    }

    /** The blog ties an article's author to the id of the user who wrote it. */
    public function isWrittenBy(?User $user): bool
    {
        return $user !== null && $this->author_id === $user->id;
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
