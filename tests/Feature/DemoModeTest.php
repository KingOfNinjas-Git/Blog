<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Author;
use App\Models\User;
use App\Support\DemoMode;
use Database\Seeders\BlogContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class DemoModeTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_starting_content_seeds_from_scratch(): void
    {
        (new BlogContentSeeder)->run();

        $this->assertSame(count(BlogContentSeeder::AUTHORS), Author::count());
        $this->assertSame(count(BlogContentSeeder::ARTICLES), Article::count());
        $this->get('/')->assertOk()->assertSee(BlogContentSeeder::ARTICLES[0]['title']);
    }

    public function test_nothing_changes_when_demo_mode_is_off(): void
    {
        (new BlogContentSeeder)->run();

        $this->get('/')->assertOk()->assertDontSee('Portfolio demo by');
    }

    public function test_the_demo_notice_shows_when_demo_mode_is_on(): void
    {
        config(['demo.enabled' => true]);
        (new BlogContentSeeder)->run();

        $this->get('/')->assertOk()->assertSee('Portfolio demo by')->assertSee('use a made-up email');
    }

    public function test_a_visitor_who_registers_can_publish_as_an_author_with_their_own_id(): void
    {
        config(['demo.enabled' => true]);
        (new BlogContentSeeder)->run();
        DemoMode::placeholderUsers();

        $this->post('/register', [
            'name' => 'Visitor', 'email' => 'visitor@example.com',
            'password' => 'password', 'password_confirmation' => 'password',
        ])->assertRedirect();

        $user = User::where('email', 'visitor@example.com')->first();
        $this->assertGreaterThan(count(BlogContentSeeder::AUTHORS), $user->id);
        $this->assertSame('Visitor', Author::find($user->id)?->name);

        $this->post('/articles', ['title' => 'My post', 'content' => 'Hello', 'category_id' => 1])->assertRedirect();
        $this->assertSame($user->id, Article::where('title', 'My post')->first()->author_id);
    }

    public function test_clean_up_removes_old_visitors_and_restores_the_starting_content(): void
    {
        config(['demo.enabled' => true]);
        (new BlogContentSeeder)->run();
        DemoMode::placeholderUsers();

        $old = User::factory()->create(['created_at' => now()->subHours(5)]);
        DemoMode::authorFor($old);
        Article::create(['title' => 'Old visitor post', 'content' => 'x', 'author_id' => $old->id, 'category_id' => 1]);
        $fresh = User::factory()->create();
        DemoMode::authorFor($fresh);
        Article::create(['title' => 'Fresh visitor post', 'content' => 'x', 'author_id' => $fresh->id, 'category_id' => 1]);
        Article::find(BlogContentSeeder::ARTICLES[0]['id'])->update(['title' => 'Vandalised']);

        Cache::forget('demo-cleanup');
        DemoMode::cleanUp();

        $this->assertNull(User::find($old->id));
        $this->assertFalse(Article::where('title', 'Old visitor post')->exists());
        $this->assertTrue(Article::where('title', 'Fresh visitor post')->exists());
        $this->assertSame(BlogContentSeeder::ARTICLES[0]['title'], Article::find(BlogContentSeeder::ARTICLES[0]['id'])->title);
        $this->assertSame(count(BlogContentSeeder::AUTHORS), User::where('email', 'like', '%@blog.demo')->count());
    }
}
