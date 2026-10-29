<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Author;
use App\Models\Category;
use App\Models\User;
use App\Support\ArticleImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ArticleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public'); // never touch the real pictures in storage/app/public
    }

    /** The blog ties an article's author to the id of the user, so a writer is a user plus an author with the same id. */
    private function writer(string $name = 'Writer'): User
    {
        $user = User::factory()->create(['name' => $name]);
        Author::unguarded(fn () => Author::create(['id' => $user->id, 'name' => $name]));

        return $user;
    }

    private function article(User $writer, array $attributes = []): Article
    {
        $category = Category::first() ?? Category::create(['name' => 'GAMES']);

        return Article::create($attributes + ['title' => 'An article', 'content' => 'Some text', 'author_id' => $writer->id, 'category_id' => $category->id]);
    }

    public function test_the_list_filters_by_category_and_by_title_or_author(): void
    {
        $ana = $this->writer('Ana');
        $rui = $this->writer('Rui');
        $games = Category::create(['name' => 'GAMES']);
        $sports = Category::create(['name' => 'SPORTS']);
        $this->article($ana, ['title' => 'Zelda review', 'category_id' => $games->id]);
        $this->article($rui, ['title' => 'Football night', 'category_id' => $sports->id]);

        $titles = fn (string $url) => $this->get($url)->assertOk()->viewData('articles')->pluck('title')->all();

        $this->assertSame(['Football night', 'Zelda review'], $titles('/')); // newest first
        $this->assertSame(['Zelda review'], $titles('/articles?category='.$games->id));
        $this->assertSame(['Football night'], $titles('/articles?search=football'));
        $this->assertSame(['Zelda review'], $titles('/articles?search=@Ana'));
        $this->get('/articles?search=nothing-like-this')->assertSee('No articles found.');
        $this->get('/categories/'.$games->id)->assertRedirect(route('articles.index', ['category' => $games->id]));
        $this->get('/authors/'.$ana->id)->assertRedirect(route('articles.index', ['search' => '@Ana']));
    }

    public function test_an_article_page_shows_its_comments_and_a_missing_one_is_not_found(): void
    {
        $article = $this->article($this->writer('Ana'), ['title' => 'Read me']);
        $article->comments()->create(['user_id' => $this->writer('Bea')->id, 'content' => 'Nice one']);

        $this->get("/articles/{$article->id}")->assertOk()->assertSee('Read me')->assertSee('Nice one')->assertSee('log in');
        $this->get('/articles/9999')->assertNotFound();
    }

    public function test_guests_are_sent_to_the_login_page_for_everything_that_writes(): void
    {
        $article = $this->article($this->writer());

        $this->get('/articles/create')->assertRedirect('/login');
        $this->post('/articles', [])->assertRedirect('/login');
        $this->get("/articles/{$article->id}/edit")->assertRedirect('/login');
        $this->put("/articles/{$article->id}", [])->assertRedirect('/login');
        $this->delete("/articles/{$article->id}")->assertRedirect('/login');
        $this->post("/articles/{$article->id}/like")->assertRedirect('/login');
        $this->post("/articles/{$article->id}/comments", ['content' => 'x'])->assertRedirect('/login');
        $this->assertSame(1, Article::count());
    }

    public function test_a_signed_in_user_can_write_an_article_with_a_picture_that_is_shrunk(): void
    {
        $user = $this->writer();
        $category = Category::create(['name' => 'GAMES']);

        $this->actingAs($user)->post('/articles', [
            'title' => 'With a picture', 'content' => 'Body', 'category_id' => $category->id,
            'image' => UploadedFile::fake()->image('big.jpg', 2400, 1200),
        ])->assertRedirect(route('articles.index'));

        $article = Article::firstWhere('title', 'With a picture');
        $this->assertSame($user->id, $article->author_id);
        $this->assertStringEndsWith('.webp', $article->image);
        Storage::disk('public')->assertExists($article->image);
        $this->assertSame(1000, getimagesize(Storage::disk('public')->path($article->image))[0]);

        // The list gets a smaller copy, offered as a srcset; deleting the article removes both pictures.
        $thumb = ArticleImage::thumbPath($article->image);
        Storage::disk('public')->assertExists($thumb);
        $this->assertSame(600, getimagesize(Storage::disk('public')->path($thumb))[0]);
        $this->get('/')->assertSee($thumb.' 600w', false);
        $this->actingAs($user)->delete(route('articles.destroy', $article));
        Storage::disk('public')->assertMissing([$article->image, $thumb]);
    }

    public function test_missing_small_pictures_can_be_made_for_pictures_that_are_already_stored(): void
    {
        $old = Storage::disk('public')->putFileAs('images', UploadedFile::fake()->image('seed.png', 1000, 600), 'seed.webp');
        $this->article($this->writer(), ['image' => $old]);

        $this->artisan('articles:thumbs')->assertSuccessful();

        Storage::disk('public')->assertExists('images/seed-600.webp');
        $this->assertNull(ArticleImage::srcset(null));
    }

    public function test_pages_have_titles_that_say_what_they_are(): void
    {
        $article = $this->article($this->writer(), ['title' => 'A very specific title']);

        $this->get('/')->assertSee('<title>My Blog | Stories, ideas and tips</title>', false);
        $this->get(route('articles.show', $article))->assertSee('<title>A very specific title | My Blog</title>', false);
    }

    public function test_an_article_needs_a_title_content_and_a_real_category(): void
    {
        $this->actingAs($this->writer())->post('/articles', ['title' => '', 'content' => '', 'category_id' => 999])
            ->assertSessionHasErrors(['title', 'content', 'category_id']);
        $this->assertSame(0, Article::count());
    }

    public function test_only_the_author_can_edit_or_delete_an_article(): void
    {
        $owner = $this->writer('Owner');
        $other = $this->writer('Other');
        $article = $this->article($owner);

        $this->actingAs($other)->get("/articles/{$article->id}/edit")->assertForbidden();
        $this->actingAs($other)->put("/articles/{$article->id}", ['title' => 'Hacked', 'content' => 'x', 'category_id' => $article->category_id])->assertForbidden();
        $this->actingAs($other)->delete("/articles/{$article->id}")->assertForbidden();
        $this->assertSame('An article', $article->fresh()->title);

        $this->actingAs($owner)->get("/articles/{$article->id}/edit")->assertOk();
        $this->actingAs($owner)->put("/articles/{$article->id}", ['title' => 'Better title', 'content' => 'x', 'category_id' => $article->category_id])
            ->assertRedirect(route('articles.index'));
        $this->assertSame('Better title', $article->fresh()->title);
    }

    public function test_edit_and_delete_buttons_only_show_for_the_author(): void
    {
        $owner = $this->writer('Owner');
        $article = $this->article($owner);

        $this->get('/')->assertDontSee('Edit this article');
        $this->actingAs($this->writer('Other'))->get('/')->assertDontSee('Edit this article');
        $this->actingAs($owner)->get('/')->assertSee('Edit this article')->assertSee('Delete this article');
    }

    public function test_replacing_a_picture_and_deleting_an_article_remove_the_old_files(): void
    {
        $owner = $this->writer();
        $old = UploadedFile::fake()->image('old.jpg')->store('images', 'public');
        $article = $this->article($owner, ['image' => $old]);

        $this->actingAs($owner)->put("/articles/{$article->id}", [
            'title' => 'Same', 'content' => 'x', 'category_id' => $article->category_id, 'image' => UploadedFile::fake()->image('new.jpg'),
        ]);
        Storage::disk('public')->assertMissing($old);
        $current = $article->fresh()->image;
        Storage::disk('public')->assertExists($current);

        $this->actingAs($owner)->delete("/articles/{$article->id}")->assertRedirect(route('articles.index'));
        Storage::disk('public')->assertMissing($current);
        $this->assertSame(0, Article::count());
    }

    public function test_an_article_can_be_liked_once_per_user(): void
    {
        $article = $this->article($this->writer());
        $reader = User::factory()->create();

        $this->actingAs($reader)->post("/articles/{$article->id}/like")->assertSessionHas('message', 'Article liked successfully!');
        $this->actingAs($reader)->post("/articles/{$article->id}/like")->assertSessionHas('message', 'You have already liked this article.');
        $this->assertSame(1, $article->reactions()->count());
    }

    public function test_a_comment_is_saved_checked_and_needs_an_existing_article(): void
    {
        $article = $this->article($this->writer());
        $reader = User::factory()->create();

        $this->actingAs($reader)->post("/articles/{$article->id}/comments", ['content' => 'Great read'])->assertRedirect(route('articles.show', $article));
        $this->assertSame('Great read', $article->comments()->first()->content);

        $this->actingAs($reader)->post("/articles/{$article->id}/comments", ['content' => ''])->assertSessionHasErrors('content');
        $this->actingAs($reader)->post("/articles/{$article->id}/comments", ['content' => str_repeat('x', 1001)])->assertSessionHasErrors('content');
        $this->actingAs($reader)->post('/articles/9999/comments', ['content' => 'Lost'])->assertNotFound();
        $this->assertSame(1, $article->comments()->count());
    }

    public function test_the_list_can_be_ordered_by_likes_or_by_comments(): void
    {
        $writer = $this->writer();
        $quiet = $this->article($writer, ['title' => 'Quiet one']);
        $liked = $this->article($writer, ['title' => 'Liked one']);
        $talked = $this->article($writer, ['title' => 'Talked about']);
        $fans = User::factory()->count(2)->create();
        foreach ($fans as $fan) {
            $liked->reactions()->create(['user_id' => $fan->id, 'type' => 'like']);
            $talked->comments()->create(['user_id' => $fan->id, 'content' => 'Hi']);
        }
        $talked->comments()->create(['user_id' => $fans[0]->id, 'content' => 'Again']);

        $titles = fn (string $url) => $this->get($url)->assertOk()->viewData('articles')->pluck('title')->all();

        $this->assertSame(['Talked about', 'Liked one', 'Quiet one'], $titles('/')); // newest first
        $this->assertSame(['Liked one', 'Talked about', 'Quiet one'], $titles('/?sort=popular'));
        $this->assertSame(['Talked about', 'Liked one', 'Quiet one'], $titles('/?sort=discussed'));
        $this->assertSame(['Talked about', 'Liked one', 'Quiet one'], $titles('/?sort=nonsense')); // unknown order: the default
    }

    public function test_the_first_page_features_the_newest_article_and_the_most_liked_are_trending(): void
    {
        $writer = $this->writer();
        $this->article($writer, ['title' => 'Old news']);
        $this->article($writer, ['title' => 'Middle']);
        $newest = $this->article($writer, ['title' => 'Brand new']);
        $newest->reactions()->create(['user_id' => User::factory()->create()->id, 'type' => 'like']);

        $page = $this->get('/')->assertOk();
        $this->assertTrue($page->viewData('featured')->is($newest)); // the newest of them all
        $this->assertSame(['Middle', 'Old news'], $page->viewData('items')->pluck('title')->all()); // not repeated in the grid
        $this->assertSame(['Brand new'], $page->viewData('trending')->pluck('title')->all()); // only articles that have a like
        $page->assertSee('Trending now')->assertSee('Latest');

        // No big card or trending list while searching, filtering or choosing another order
        foreach (['/?search=Old', '/?category='.Category::first()->id.'&page=2', '/?sort=popular'] as $url) {
            $this->get($url)->assertOk()->assertViewHas('featured', null);
        }
        $this->get('/?sort=popular')->assertDontSee('Trending now');
    }

    public function test_an_article_page_shows_reading_time_paragraphs_and_more_to_read(): void
    {
        $writer = $this->writer();
        $article = $this->article($writer, ['title' => 'Long read', 'content' => "First paragraph.\n\nSecond <b>paragraph</b>.\nSame paragraph, new line."]);
        $this->article($writer, ['title' => 'Another one']);

        $this->assertSame(1, $article->readingMinutes());
        $this->assertSame(3, Article::make(['content' => str_repeat('word ', 450)])->readingMinutes());
        $this->assertSame(['First paragraph.', "Second <b>paragraph</b>.\nSame paragraph, new line."], $article->paragraphs());

        $this->get(route('articles.show', $article))->assertOk()
            ->assertSee('<p>First paragraph.</p>', false)
            ->assertSee('Second &lt;b&gt;paragraph&lt;/b&gt;.<br />', false) // text is escaped, a single new line becomes a line break
            ->assertSee('1 min read')
            ->assertSee('Keep reading')->assertSee('Another one')
            ->assertSee('og:title" content="Long read"', false);
    }

    public function test_a_liked_article_shows_as_liked_to_the_person_who_liked_it(): void
    {
        $article = $this->article($this->writer());
        $reader = User::factory()->create();
        $article->reactions()->create(['user_id' => $reader->id, 'type' => 'like']);

        $this->actingAs($reader)->get('/')->assertViewHas('likedIds', [$article->id]);
        $this->actingAs($reader)->get(route('articles.show', $article))->assertViewHas('liked', true)->assertSee('You liked this article');
        $this->actingAs(User::factory()->create())->get(route('articles.show', $article))->assertViewHas('liked', false);
        $this->get('/')->assertViewHas('likedIds', []);
    }

    public function test_the_excerpt_is_plain_text_and_short(): void
    {
        $text = Article::make(['content' => "<b>Bold</b>   start\n\n".str_repeat('long ', 100)])->excerpt(40);

        $this->assertStringNotContainsString('<b>', $text);
        $this->assertStringStartsWith('Bold start long', $text);
        $this->assertStringEndsWith('...', $text);
        $this->assertLessThanOrEqual(43, mb_strlen($text)); // 40 characters plus the dots
    }

    public function test_a_picture_description_is_saved_and_read_out_instead_of_the_title(): void
    {
        $user = $this->writer();
        $category = Category::create(['name' => 'GAMES']);

        $this->actingAs($user)->post('/articles', [
            'title' => 'Barn story', 'content' => 'Body', 'category_id' => $category->id,
            'image' => UploadedFile::fake()->image('barn.jpg', 800, 500), 'image_alt' => 'A red barn in a green field',
        ])->assertRedirect();
        $described = Article::firstWhere('title', 'Barn story');
        $this->assertSame('A red barn in a green field', $described->image_alt);
        $this->get(route('articles.show', $described))->assertSee('alt="A red barn in a green field"', false);

        $this->actingAs($user)->post('/articles', [
            'title' => 'No description', 'content' => 'Body', 'category_id' => $category->id, 'image' => UploadedFile::fake()->image('x.jpg', 800, 500),
        ])->assertRedirect();
        $this->get(route('articles.show', Article::firstWhere('title', 'No description')))->assertSee('alt="No description"', false);

        $this->actingAs($user)->post('/articles', ['title' => 'Too long', 'content' => 'x', 'category_id' => $category->id, 'image_alt' => str_repeat('a', 201)])->assertSessionHasErrors('image_alt');
    }

    public function test_the_fonts_come_from_this_site_not_from_google(): void
    {
        $this->get('/')->assertOk()->assertDontSee('fonts.googleapis.com')->assertDontSee('fonts.gstatic.com');
    }
}
