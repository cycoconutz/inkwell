# Inkwell

A small, self-contained PHP blog and publishing engine. No third-party packages, strict types throughout, PDO with prepared statements, CSRF protection, safe Markdown rendering, and a production-friendly Docker setup.

## What it is

- Server-rendered PHP 8.3 app (front controller + small router)
- MySQL/MariaDB via PDO
- Fallback PSR-4 autoloader (works without Composer)
- Session auth for a single admin, CSRF tokens on state-changing forms
- Safe Markdown subset for post bodies
- Health endpoint (`/healthz`) for platform checks
- Clean layouts, responsive CSS, minimal JavaScript

## Quick start (local, Docker)

Prerequisites: Docker and Docker Compose (v2). 

```bash
cd inkwell
docker compose up --build
```

- App: http://localhost:8080
- Admin: http://localhost:8080/login
  - email: `admin@inkwell.test`
  - password: `password`
- MySQL: localhost:3306 (user `inkwell`, pass `inkwell`, db `inkwell`) — only while the stack runs

Migrations and a demo post run automatically on first boot (idempotent). Change admin credentials via `.env` or environment variables.

## Local PHP (optional, without Docker)

Only useful to run unit tests if you have PHP 8.3 with PDO MySQL. No web server included here; use PHP's built-in server:

```bash
php -S localhost:8080 -t public public/router.php
```

You still need MySQL reachable via `.env`.

## Environment variables

Copy `.env.example` to `.env` and adjust. Defaults work for Docker Compose.

| Key | Default | Notes |
|---|---|---|
| APP_NAME | Inkwell | Display name |
| APP_ENV | production | `local` shows full error traces |
| APP_DEBUG | false | Set `true` in dev |
| APP_URL | http://localhost:8080 | Used for generated URLs |
| DB_HOST | 127.0.0.1 | `db` in Docker |
| DB_PORT | 3306 | |
| DB_DATABASE | inkwell | |
| DB_USERNAME | root | `inkwell` in Docker |
| DB_PASSWORD | | `inkwell` in Docker |
| SESSION_NAME | inkwell_session | |
| ADMIN_EMAIL | admin@inkwell.test | Seeded on boot |
| ADMIN_PASSWORD | password | Seeded on boot |

## Tests

PHPUnit 11 is included in `composer.json` (dev). If Composer is unavailable locally, install dependencies inside Docker or in a PHP environment with Composer.

```bash
composer install --dev
vendor/bin/phpunit
```

Tests are pure (no MySQL required): `CsrfTest`, `RequestTest`, `EnvTest`, `ViewTest`, `MarkdownTest`.

## Security notes

- Prepared statements only (no raw interpolation into SQL). The `table` name on models is a class constant, not user input.
- HTML escaped via `e()` helper (single boundary). Markdown renderer escapes first, then whitelists a tiny tag set.
- Links in Markdown: only `http(s)://`, `mailto:`, `/`, and `#`; external links get `rel="noopener noreferrer" target="_blank"`.
- CSRF tokens on all POST/PUT/DELETE forms.
- Login rate-limited (5 attempts per 15 minutes).
- Secure session defaults: HttpOnly, SameSite=Lax, Secure when behind HTTPS/Render proxy.
- No third-party code paths at runtime; no secrets committed.

## Deployment (Render)

`render.yaml` is provided for Render Web Service + Managed MySQL. Set `APP_URL` to your service URL (e.g. `https://inkwell.onrender.com`) and set `ADMIN_EMAIL`/`ADMIN_PASSWORD` as Render environment variables. Health check is `/healthz`.

## Project structure

```
inkwell/
  config/        routes, app config
  database/      schema.sql
  docker/        entrypoint.sh
  public/        index.php, .htaccess, assets
  scripts/       migrate.php, seed.php
  src/           App core, Support, Models, Controllers
  tests/         unit tests
  views/         layouts, partials, pages, errors
  Dockerfile, docker-compose.yml, render.yaml, phpunit.xml
```

## Notes on design choices

- Zero runtime Composer dependencies to keep the audit surface tiny and the image fast to inspect.
- Fallback autoloader mirrors Composer's PSR-4 mapping for `App\\` -> `src/`.
- Markdown is intentionally minimal (headings, lists, blockquote, fenced code, hr, inline bold/italic/code/links). If you need tables/images, extend it deliberately.
- Errors: in `APP_ENV=local` full trace; in production a friendly 500 view. 404/403 rendered via views.

Built as a clean, demonstrable PHP scaffold.