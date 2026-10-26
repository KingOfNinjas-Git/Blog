<?php

namespace App\Support;

use App\Models\Article;
use App\Models\Author;
use App\Models\User;
use Database\Seeders\BlogContentSeeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/** Everything that only exists for the public portfolio demo (see config/demo.php). */
class DemoMode
{
    /** Placeholder accounts for the starting authors; nobody can sign in as them. */
    public const PLACEHOLDER_DOMAIN = '@blog.demo';

    public static function enabled(): bool
    {
        return (bool) config('demo.enabled');
    }

    /**
     * The blog ties an article's author to the user's id, so every starting author needs a user with the
     * same id. This creates those accounts (with random passwords) so new visitors get higher ids.
     */
    public static function placeholderUsers(): void
    {
        foreach (BlogContentSeeder::AUTHORS as $author) {
            User::unguarded(fn () => User::firstOrCreate(
                ['id' => $author['id']],
                ['name' => $author['name'], 'email' => Str::lower($author['name']).self::PLACEHOLDER_DOMAIN, 'password' => Str::random(40)],
            ));
        }
    }

    /** A visitor who registers becomes an author with the same id, so they can publish articles. */
    public static function authorFor(User $user): void
    {
        Author::unguarded(fn () => Author::updateOrCreate(['id' => $user->id], ['name' => $user->name]));
    }

    /** Puts the blog back to how it started. Runs at most once an hour, after the response has been sent. */
    public static function cleanUp(): void
    {
        if (! self::enabled() || ! Cache::add('demo-cleanup', 1, 3600)) {
            return;
        }

        // Visitor accounts older than the keep time go, with their comments and likes.
        User::where('email', 'not like', '%'.self::PLACEHOLDER_DOMAIN)
            ->where('created_at', '<', now()->subHours(config('demo.keep_hours')))
            ->delete();

        // Their author rows go too, and with them the articles they wrote.
        Author::whereNotIn('id', User::select('id'))->delete();

        // The starting content is put back as it was (edits and deletions by visitors are undone).
        (new BlogContentSeeder)->run();

        // Pictures that no remaining article uses.
        $used = Article::pluck('image')->filter()->all();
        $disk = Storage::disk('public');
        foreach ($disk->files('images') as $file) {
            if (! in_array($file, $used, true)) {
                $disk->delete($file);
            }
        }
    }
}
