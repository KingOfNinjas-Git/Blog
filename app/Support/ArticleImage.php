<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/** Saves the picture of an article (and a smaller copy of it for the article list). */
class ArticleImage
{
    /** A phone photo is several megabytes; a page never shows a picture wider than this. */
    public const MAX_WIDTH = 1000;

    /** The smaller copy used by the article list, so phones do not download the full picture. */
    public const THUMB_WIDTH = 600;

    /**
     * Stores the upload on the "public" disk as a WebP picture at most 1000 px wide, plus a 600 px copy,
     * and returns its path ("images/<random>.webp"). Animated GIFs, and servers without WebP support,
     * keep the original file.
     */
    public static function store(UploadedFile $file): string
    {
        if ($file->getMimeType() !== 'image/gif' && function_exists('imagewebp') && ($image = @imagecreatefromstring($file->get()))) {
            if (imagesx($image) > self::MAX_WIDTH) {
                $image = imagescale($image, self::MAX_WIDTH);
            }

            imagepalettetotruecolor($image);
            imagealphablending($image, false);
            imagesavealpha($image, true);

            $path = 'images/'.Str::random(40).'.webp';
            Storage::disk('public')->put($path, self::webp($image, 78));
            Storage::disk('public')->put(self::thumbPath($path), self::webp(self::scaled($image, self::THUMB_WIDTH), 74));

            return $path;
        }

        return $file->store('images', 'public');
    }

    public static function delete(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete([$path, self::thumbPath($path)]);
        }
    }

    /** "images/abc.webp" -> "images/abc-600.webp" */
    public static function thumbPath(string $path): string
    {
        return preg_replace('/\.webp$/', '-'.self::THUMB_WIDTH.'.webp', $path);
    }

    /** The srcset for the list picture, or null when the article has no smaller copy. */
    public static function srcset(?string $path): ?string
    {
        if (! $path || ! Storage::disk('public')->exists(self::thumbPath($path)) || self::thumbPath($path) === $path) {
            return null;
        }

        return asset('storage/'.self::thumbPath($path)).' '.self::THUMB_WIDTH.'w, '.asset('storage/'.$path).' '.self::MAX_WIDTH.'w';
    }

    /** Makes the missing small copies of pictures that are already stored. Returns how many it made. */
    public static function makeMissingThumbs(iterable $paths): int
    {
        $disk = Storage::disk('public');
        $made = 0;

        foreach ($paths as $path) {
            $thumb = self::thumbPath((string) $path);

            if (! $path || $thumb === $path || ! $disk->exists($path) || $disk->exists($thumb)) {
                continue;
            }

            if (($image = @imagecreatefromstring($disk->get($path))) && function_exists('imagewebp')) {
                imagepalettetotruecolor($image);
                imagealphablending($image, false);
                imagesavealpha($image, true);
                $disk->put($thumb, self::webp(self::scaled($image, self::THUMB_WIDTH), 74));
                $made++;
            }
        }

        return $made;
    }

    private static function scaled(\GdImage $image, int $width): \GdImage
    {
        return imagesx($image) > $width ? imagescale($image, $width) : $image;
    }

    private static function webp(\GdImage $image, int $quality): string
    {
        ob_start();
        imagewebp($image, null, $quality);

        return ob_get_clean();
    }
}
