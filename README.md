# Ecommerce

A small ecommerce web application built with [Laravel 12](https://laravel.com) and [Tailwind CSS](https://tailwindcss.com).

## Features

- Storefront for browsing products and checking out
- Admin panel for managing products and orders
- Authentication scaffolded with [Laravel Breeze](https://laravel.com/docs/starter-kits#laravel-breeze)
- Order confirmation and status update emails
- Database seeders and factories for quick local setup

## Tech Stack

- PHP 8.2+ / Laravel 12
- Blade views + Tailwind CSS (Vite)
- SQLite (default) — swap via `.env`
- Pest for testing

## Getting Started

```bash
git clone <repo-url> ecommerce
cd ecommerce

composer install
npm install

cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
```

Configure your mail driver in `.env` (required for order emails), then start the dev stack:

```bash
composer dev
```

This runs the app server, queue listener, log viewer, and Vite in parallel.

## Useful Commands

| Command | Description |
| --- | --- |
| `php artisan serve` | Start the app on `http://localhost:8000` |
| `php artisan migrate:fresh --seed` | Reset and reseed the database |
| `npm run dev` | Start the Vite dev server |
| `npm run build` | Build production assets |
| `composer test` | Run the test suite |

## Project Structure

- `app/Http/Controllers/Site` — storefront controllers (catalog, checkout, etc.)
- `app/Http/Controllers/Admin` — admin panel controllers
- `app/Mail` — order-related mailables
- `routes/web.php`, `routes/admin.php`, `routes/auth.php` — route definitions
- `resources/views` — Blade templates, including `mails/` for email layouts

## License

Open-sourced under the [MIT license](https://opensource.org/licenses/MIT).
