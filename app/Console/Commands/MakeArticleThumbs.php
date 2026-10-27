<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Support\ArticleImage;
use Illuminate\Console\Command;

class MakeArticleThumbs extends Command
{
    protected $signature = 'articles:thumbs';

    protected $description = 'Make the small (600 px) copy of every article picture that does not have one yet';

    public function handle(): int
    {
        $made = ArticleImage::makeMissingThumbs(Article::whereNotNull('image')->pluck('image'));
        $this->info("Made {$made} small picture(s).");

        return self::SUCCESS;
    }
}
