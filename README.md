# AfraaCMS

AfraaCMS is a Laravel-based content management system built for a school/NGO-style organization site — pages, news, courses, students, donations, galleries, team members, and more, all managed through an admin panel with role-based permissions.

## Features

- **Pages & content** — dynamic pages built from reusable sections, banners, and SEO metadata
- **News & stories** — categorized news posts and stories
- **Courses & students** — course listings, student records, and enrollments
- **Certificates** — certificate issuance/lookup
- **Donations** — donation records
- **Galleries** — image/media galleries with gallery items
- **Team & visitors** — team members/categories, featured visitors, and a visitor book
- **Menus** — configurable site navigation (menus and menu items)
- **Media library** — powered by Spatie Media Library
- **Roles & permissions** — powered by Spatie Laravel Permission
- **Activity log** — auditing powered by Spatie Activitylog
- **Settings** — site-wide settings (e.g. toggling registration on/off) via `app/CMS/Services/SettingService`

## Tech Stack

- PHP 8.3+, Laravel 13
- SQLite (default) / any Laravel-supported database
- Tailwind CSS 4, Alpine.js, Vite
- Quill (rich text), Cropper.js (image cropping), Sortable.js (drag-and-drop ordering)

## Getting Started

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate
npm install
npm run build
```

Or run the bundled setup script:

```bash
composer run setup
```

### Local development

```bash
composer run dev
```

This runs the PHP dev server, queue listener, log tailer (Pail), and Vite dev server concurrently.

### Running tests

```bash
composer run test
```

## Project Structure

- `app/CMS/` — CMS-specific services, helpers, caching, menu/rendering/SEO logic
- `app/Http/Controllers/Admin/` — admin panel controllers (one per resource)
- `app/Http/Controllers/Auth/` — authentication (Laravel Breeze-based)
- `app/Models/` — Eloquent models for all CMS resources
- `resources/views/` — Blade views (admin panel, public site, auth)

## License

MIT
