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

    protected $fillable = ['title', 'content', 'author_id', 'category_id', 'image', 'image_alt', 'status'];

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

    /** A short plain-text teaser for the cards (the formatting marks are left out). */
    public function excerpt(int $limit = 140): string
    {
        $text = html_entity_decode(strip_tags($this->html()), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return Str::limit(trim(preg_replace('/\s+/', ' ', $text)), $limit);
    }

    /**
     * The text as safe HTML. Writers can use simple formatting (## headings, **bold**, *italic*, lists, > quotes, [links](https://...));
     * HTML typed into the text is removed, a blank line starts a new paragraph and a single new line becomes a line break.
     */
    public function html(): string
    {
        $html = Str::markdown((string) $this->content, [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
            'renderer' => ['soft_break' => "<br />\n"],
        ]);

        // Links to other sites open in a new tab without handing them the page
        return preg_replace('/<a href="(https?:\/\/[^"]+)"/', '<a href="$1" target="_blank" rel="noopener noreferrer"', $html);
    }

    /** Only these can be seen by everyone; a draft is private to its author. */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
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
