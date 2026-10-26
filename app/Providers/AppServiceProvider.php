<?php

namespace App\Providers;

use App\Support\DemoMode;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Portfolio demo: a visitor who registers also becomes an author, so they can publish.
        Event::listen(Registered::class, function (Registered $event) {
            if (DemoMode::enabled()) {
                DemoMode::authorFor($event->user);
            }
        });
    }
}
