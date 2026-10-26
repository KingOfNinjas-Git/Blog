<?php

// Public portfolio demo: a notice on every page and automatic clean-up of what visitors add.
// Off by default; a real blog leaves it off.
return [
    'enabled' => (bool) env('DEMO_MODE', false),

    // Accounts that visitors register (and what they wrote) are removed after this many hours.
    'keep_hours' => (int) env('DEMO_KEEP_HOURS', 3),

    'portfolio_url' => env('PORTFOLIO_URL', 'https://franciscowebdev.com'),
];
