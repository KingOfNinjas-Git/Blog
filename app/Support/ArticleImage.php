<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/** Saves the picture of an article. */
class ArticleImage
{
    /** A phone photo is several megabytes; a page never shows a picture wider than this. */
    public const MAX_WIDTH = 1000;

    /**
     * Stores the upload on the "public" disk as a WebP picture at most 1000 px wide, and returns its path
     * ("images/<random>.webp"). Animated GIFs, and servers without WebP support, keep the original file.
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

            ob_start();
            imagewebp($image, null, 78);
            $webp = ob_get_clean();

            $path = 'images/'.Str::random(40).'.webp';
            Storage::disk('public')->put($path, $webp);

            return $path;
        }

        return $file->store('images', 'public');
    }

    public static function delete(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }
}
