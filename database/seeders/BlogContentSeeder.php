<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Author;
use App\Models\Category;
use Illuminate\Database\Seeder;

/** The blog's starting content: 5 authors, 7 categories and 5 articles (also what the portfolio demo is restored to). */
class BlogContentSeeder extends Seeder
{
    public const AUTHORS = [
        0 => [
            'id' => 1,
            'name' => 'Gabriel',
            'created_at' => '2025-05-05 19:56:29',
            'updated_at' => '2025-05-05 19:56:29',
        ],
        1 => [
            'id' => 2,
            'name' => 'Rodrigo',
            'created_at' => '2025-05-06 11:32:13',
            'updated_at' => '2025-05-06 11:32:13',
        ],
        2 => [
            'id' => 3,
            'name' => 'Manuel',
            'created_at' => '2025-05-06 11:32:13',
            'updated_at' => '2025-05-06 11:32:13',
        ],
        3 => [
            'id' => 4,
            'name' => 'Andre',
            'created_at' => '2025-05-12 15:21:57',
            'updated_at' => '2025-05-12 15:21:57',
        ],
        4 => [
            'id' => 5,
            'name' => 'Regina',
            'created_at' => '2025-05-12 14:21:07',
            'updated_at' => '2025-05-12 14:21:07',
        ],
    ];

    public const CATEGORIES = [
        0 => [
            'id' => 1,
            'name' => 'HORROR',
            'created_at' => '2025-04-27 10:23:43',
            'updated_at' => '2025-04-27 10:23:43',
        ],
        1 => [
            'id' => 2,
            'name' => 'DRAMA',
            'created_at' => '2025-04-28 10:23:43',
            'updated_at' => '2025-04-28 10:23:43',
        ],
        2 => [
            'id' => 3,
            'name' => 'ACTION',
            'created_at' => null,
            'updated_at' => null,
        ],
        3 => [
            'id' => 4,
            'name' => 'GAMES',
            'created_at' => null,
            'updated_at' => null,
        ],
        4 => [
            'id' => 5,
            'name' => 'SPORTS',
            'created_at' => null,
            'updated_at' => null,
        ],
        5 => [
            'id' => 6,
            'name' => 'ECONOMY',
            'created_at' => null,
            'updated_at' => null,
        ],
        6 => [
            'id' => 7,
            'name' => 'WEATHER',
            'created_at' => null,
            'updated_at' => null,
        ],
    ];

    public const ARTICLES = [
        0 => [
            'id' => 24,
            'title' => 'The House at Hollow Creek: A Place Where Shadows Whisper',
            'content' => 'Hidden behind a twisted path of crooked trees and thick fog lies the House at Hollow Creek—a place long abandoned by the living, but never truly empty. Locals say the wind doesn’t just howl through its broken shutters; it whispers names, secrets, and regrets.

Built in 1892, the house was once a proud Victorian mansion, owned by the enigmatic Blackwood family. But after a string of mysterious deaths and disappearances, the estate fell silent. No one lives there now, yet candles are sometimes seen flickering in the windows. Noises—footsteps, soft crying, even laughter—echo from within, though no one dares enter after dark.

Visitors who step too close report sudden cold spots, static in the air, and a heavy sense of dread. Some claim to have seen figures moving behind the tattered curtains. Others never return to speak of what they found.

But the bravest—or the most foolish—still come, drawn by curiosity or thrill. They say the house wants to be remembered, and it won’t let you forget once you\'ve stepped inside.

So if you find yourself near Hollow Creek after sundown, beware. The house is always waiting',
            'image' => 'images/CAP1NugSQo2wL12iT16SDklMTyu8acRvxpCJUsLf.webp',
            'image_alt' => 'A crooked old mansion standing alone behind bare, twisted trees',
            'author_id' => 1,
            'category_id' => 1,
            'created_at' => '2025-05-07 09:05:26',
            'updated_at' => '2025-05-07 09:05:26',
        ],
        1 => [
            'id' => 25,
            'title' => 'Minecraft Movie: From Blocks to the Big Screen',
            'content' => 'After years of anticipation, the Minecraft movie is finally taking shape, bringing the iconic pixelated world of Mojang’s sandbox game to life on the big screen. Slated for release in 2025, the film promises to blend action, humor, and creativity—just like the game that inspired it. Directed by Jared Hess (Napoleon Dynamite) and starring Jason Momoa, the movie aims to capture the spirit of exploration and survival that made Minecraft a global phenomenon.

The story follows a teenage girl and her unlikely group of adventurers who must save the Overworld after the powerful Ender Dragon begins a path of destruction. While details remain under wraps, fans can expect familiar biomes, mobs, and the signature blocky aesthetic—all reimagined in a cinematic adventure.

As one of the best-selling games of all time, Minecraft has influenced a generation with its open-ended gameplay. The movie adaptation has the potential to reach new audiences while giving longtime fans a fresh way to experience their favorite virtual universe.',
            'image' => 'images/2oFHVOUlGLMh5Fx4bQ7Ich7IB7YNBT1xlPLIVP8O.webp',
            'image_alt' => 'Glowing turquoise ore blocks in a dark Minecraft cave',
            'author_id' => 1,
            'category_id' => 4,
            'created_at' => '2025-05-07 09:07:23',
            'updated_at' => '2025-05-12 08:13:45',
        ],
        2 => [
            'id' => 32,
            'title' => 'GTA 6 Delayed Again: Fans Left Waiting Once More',
            'content' => 'Rockstar Games has officially confirmed that Grand Theft Auto VI is facing yet another delay, pushing its highly anticipated release further down the road. Originally expected in early 2025, the game is now rumored to launch in late 2025 or even 2026, leaving fans both frustrated and eager for more news.

While Rockstar cites the need for "polish and perfection" as the main reason, many in the gaming community are speculating that internal production challenges and the pressure to live up to GTA V\'s legacy are contributing factors. After over a decade since the last mainline installment, expectations for GTA 6 are sky-high.

Set in a modern-day Vice City with a rumored duo of protagonists, GTA 6 promises a richer open-world experience, dynamic storytelling, and advanced AI. However, with each delay, fans are left wondering whether the wait will truly be worth it.

Rockstar maintains that they are committed to delivering a game that exceeds expectations—but as patience wears thin, players are hoping this will be the last delay before the long-awaited return to the criminal underworld.',
            'image' => 'images/R9mOxxlcMxTGkss0S2MHqKbAc34Pw6OgVWBCcXLF.webp',
            'image_alt' => 'Two characters leaning on a sports car on a sunny, palm-lined street',
            'author_id' => 2,
            'category_id' => 4,
            'created_at' => '2025-05-12 13:22:57',
            'updated_at' => '2025-05-12 13:22:57',
        ],
        3 => [
            'id' => 33,
            'title' => 'Hollywood Turmoil: Famous Co-Stars Caught in On-Set Feud',
            'content' => 'Tensions are reportedly running high on the set of the upcoming thriller "Silent Echoes", as insiders reveal a growing rift between lead actors Amelia Ross and Daniel Keene. According to multiple sources close to the production, what began as creative disagreements has spiraled into a full-blown feud, with shouting matches and icy silences becoming a daily occurrence.

The drama allegedly began during a heated rehearsal when Keene questioned a scene rewrite suggested by Ross, who also serves as co-producer on the film. Since then, communication between the two has broken down, with crew members saying the atmosphere on set is “unbearable.”

Despite the tension, both stars remain committed to finishing the film, but rumors are swirling that studio executives are considering bringing in conflict resolution professionals to salvage what they’re calling a “fragile production.”

Fans of both actors have taken to social media, debating who’s to blame, while tabloids are having a field day speculating whether this off-screen conflict will overshadow the movie’s eventual release.

Will "Silent Echoes" be remembered for its suspenseful plot—or the fireworks behind the scenes?

Would you like a fake gossip magazine cover or social media mockup featuring this drama?',
            'image' => 'images/zgdqiKTHxKEjAWDkSHP0rXlESBz8wP8UH7sbPJ27.webp',
            'image_alt' => 'A newspaper-style headline above the tense faces of a woman and a man',
            'author_id' => 3,
            'category_id' => 2,
            'created_at' => '2025-05-12 13:26:03',
            'updated_at' => '2025-05-12 13:26:03',
        ],
        4 => [
            'id' => 37,
            'title' => 'Weekend Weather Forecast: Sunshine with a Chance of Showers',
            'content' => 'Get ready for a mixed bag of weather this weekend! Saturday starts off bright and sunny across most regions, with temperatures reaching a comfortable 22°C (72°F) in the afternoon. Light breezes from the southwest will make it a perfect day for outdoor activities or a stroll in the park.

However, Sunday brings a shift, as clouds roll in and scattered showers are expected by late morning, especially in coastal and northern areas. Temperatures will dip slightly, hovering around 18°C (64°F), with increased humidity in the afternoon.

Don’t forget to pack an umbrella if you\'re heading out on Sunday—and make the most of Saturday\'s sunshine while it lasts! Stay tuned for updates as the forecast develops.',
            'image' => 'images/HbcOPglj3upLHkuYTaLKNaVwB1AObuEmHruONeVb.webp',
            'image_alt' => 'Green trees and white clouds under a bright blue sky',
            'author_id' => 5,
            'category_id' => 7,
            'created_at' => '2025-05-12 14:29:15',
            'updated_at' => '2025-05-12 14:29:15',
        ],
    ];

    public function run(): void
    {
        foreach (self::AUTHORS as $row) {
            Author::unguarded(fn () => Author::updateOrCreate(['id' => $row['id']], $row));
        }

        foreach (self::CATEGORIES as $row) {
            Category::unguarded(fn () => Category::updateOrCreate(['id' => $row['id']], $row));
        }

        foreach (self::ARTICLES as $row) {
            Article::unguarded(fn () => Article::updateOrCreate(['id' => $row['id']], $row));
        }
    }
}
