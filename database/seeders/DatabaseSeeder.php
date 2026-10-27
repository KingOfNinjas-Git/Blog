<?php

namespace Database\Seeders;

use App\Models\User;
use App\Support\DemoMode;
use Faker\Factory;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(BlogContentSeeder::class);

        // The portfolio demo needs a user behind each starting author (see DemoMode::placeholderUsers).
        if (DemoMode::enabled()) {
            DemoMode::placeholderUsers();
        }

        // A test account for local development only (it needs the dev-only faker package).
        if (app()->environment('local') && class_exists(Factory::class)) {
            User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);
        }
    }
}
