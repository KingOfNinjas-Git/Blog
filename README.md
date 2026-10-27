# My Blog: articles, likes and comments

A pixel-art styled blog where signed-in users write articles with a picture, and everyone can search, filter by category, like and comment.

Built with **Laravel 12, Blade, Tailwind CSS** and SQLite.

**Live demo:** https://blogapp.franciscowebdev.com (create a free account with a made-up e-mail to write, like and comment)

## What it does

- **Articles** with a title, category, body and optional picture. Pictures are shrunk to at most 1000 px and saved as WebP, with a 600 px copy for the list so phones download less.
- **Search and filter** by title, by `@author` or by category; articles with more than 10 likes get a "Popular" badge.
- **Likes and comments** for signed-in users.
- **Accounts**: register, log in, edit your profile. Only the author can edit or delete an article.
- Phone friendly, keyboard friendly, with a page title that says what each page is.

## Run it locally

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
php artisan storage:link
npm install
npm run dev        # or npm run build
php artisan serve
```

`php artisan articles:thumbs` makes the small 600 px copy of any article picture that does not have one yet (useful after upgrading an older install).

## Tests

```bash
php artisan test
./vendor/bin/pint --test   # code style
```

The tests use an in-memory database and a fake `public` disk, so they never touch the real pictures in `storage/app/public`. GitHub Actions runs them, the code style check and the front-end build on every push.

## The portfolio demo

On the live demo `DEMO_MODE=true` is set. It shows a notice bar, puts the starting articles back as they were about once an hour (visitor accounts, their articles, likes and comments are removed after a few hours) and asks search engines not to index the demo (`X-Robots-Tag: noindex`). With the setting off it is a normal blog.

Part of the portfolio at https://franciscowebdev.com.
