# Fieldnotes

A small editorial blog built with plain PHP, MySQL and Smarty. No framework, ORM or client-side JavaScript.

## Run with Docker

Requires Docker Desktop (or Docker Engine with Compose). From the project directory:

```sh
docker compose up -d --build
docker compose exec web php bin/seed.php
```

Open **http://localhost:8080**.

The first build installs the locked Composer dependencies. MySQL creates the schema automatically on its first start. The seeder adds 3 categories and 18 articles, including articles in multiple categories chosen by topic. Each category has 7–10 articles, enough to demonstrate pagination. Images and compiled CSS are included; Node.js is not needed to run the site.

The seeder requires empty tables and refuses to change an existing dataset. All inserts run in one transaction. Initial view counts are illustrative; publication dates are relative to the seed date.

```sh
docker compose down          # Stop the site; keep database contents
docker compose up -d         # Start it again
docker compose up -d --build # Rebuild after changing source files
```

To discard **all local blog data** and start again:

```sh
docker compose down -v
docker compose up -d --build
docker compose exec web php bin/seed.php
```

The Compose credentials are local development defaults. Override `DB_PASSWORD` and `DB_ROOT_PASSWORD` in a root `.env` file if needed, before creating the database volume. The web port binds to loopback only; MySQL is not exposed to the host.

## Pages and behaviour

| URL | Behaviour |
| --- | --- |
| `/` | Non-empty categories, each with its three latest published articles and an “All articles” link. |
| `/category/1` | Category name, description and six articles per page, newest first. |
| `/category/1?sort=views&page=2` | Most-viewed articles, second page. Pagination preserves the sort order. |
| `/article/1` | Image, title, description, text, publication date, all categories, views and up to three related articles. |

- Sorting is descending. Publication date and then ID break ties; views sorting uses views first, then publication date and ID.
- Article links from a category carry its ID, sort order and page number. The article breadcrumb and back link return to that list; direct visits return to the journal. Return links are built from validated parameters, never from an arbitrary URL.
- Each article card is one keyboard-accessible link. Numbered pagination marks the current page and keeps a compact range around it.
- Related articles share at least one category. More shared categories rank first, followed by newer publication dates and higher IDs. The current article is excluded, and each result appears once. If fewer than three related articles exist, only those are shown.
- Every article `GET` adds one view, including a refresh. A `HEAD` request does not increment it. This is a page-view counter, not a unique-reader count; no cookies or visitor tracking are used.
- Dates are stored and compared in UTC. Future-dated articles stay out of listings and return 404 until publication.
- Empty categories have an empty state and are omitted from the homepage sections. Unknown pages return 404; invalid sorting or page parameters return 400; unsupported HTTP methods return 405.

## Structure

```text
assets/scss/app.scss       Stylesheet source
bin/seed.php              CLI sample-data seeder
database/schema.sql       Tables, indexes and foreign keys
docker/apache.conf        Public document root and URL rewriting
public/index.php          Request handling and template data
public/assets/            Compiled CSS and local SVG illustrations
src/BlogRepository.php    Blog queries using PDO
src/database.php          Database connection
templates/                Smarty layouts, pages and shared article card
var/smarty/               Compiled templates (not committed)
```

There are three page routes, so request handling stays in one front controller. The repository keeps SQL out of templates and request handling; Smarty handles presentation. There is no service container or custom routing framework.

### Database

`categories` and `posts` have a many-to-many relationship through `category_post`. Its composite primary key prevents duplicate assignments; foreign keys prevent dangling relationships. Each seeded article has one or two categories.

The homepage uses `ROW_NUMBER()` partitioned by category, so it retrieves at most three posts per category without issuing a query for each category. Category pages use a count query and a limited page query. Article bodies are only loaded on the article page.

User-supplied values use prepared statements with native PDO prepares. Sort expressions come from a fixed allowlist; pagination limits are bound as integers. The view counter uses `views = views + 1` in SQL, avoiding lost increments from concurrent read-modify-write requests.

Smarty HTML auto-escaping is enabled. Article bodies are plain text split into paragraphs, not arbitrary HTML. The server exposes only `public/`; templates, credentials and dependencies are outside the document root. Unexpected exceptions are logged server-side and return a generic 500 page.

### Styles

Requires Node.js 20.19+ if you want to edit SCSS:

```sh
npm ci
npm run build
docker compose up -d --build
```

Commit both the SCSS and the generated `public/assets/app.css`. The dark layout adapts to desktop and mobile, uses semantic landmarks, visible keyboard focus, labelled controls and reduced-motion preferences. All illustrations are included as editable SVG files. Space Grotesk is served locally from `public/assets/fonts/`, with its SIL Open Font License included in `OFL.txt`. There are no external font, image or script requests.

## Run without Docker

Use PHP 8.1+ with `pdo_mysql`, Composer, MySQL 8.0+ and Apache 2.4 with `mod_rewrite`. The included Docker environment uses PHP 8.4 and MySQL 8.4.

1. Create an empty database and an application user with access to it.
2. Import `database/schema.sql` into that database.
3. Export `DB_HOST`, `DB_NAME`, `DB_USER` and `DB_PASSWORD` into the PHP process environment.
4. Run `composer install --no-dev` and `php bin/seed.php`.
5. Configure Apache using `docker/apache.conf`, adapting the filesystem paths. The document root must be `public/`; `var/smarty/` must be writable by PHP.

Only Smarty and its required dependency are installed through Composer. The lockfiles record the exact PHP and stylesheet dependency versions.
