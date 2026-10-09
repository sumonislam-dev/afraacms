# Deploying AfraaCMS to Hostinger

How to put AfraaCMS on Hostinger shared hosting (hPanel, with SSH) and how
to ship updates afterwards. Replace these placeholders throughout:

| Placeholder | Example | Where to find it |
|---|---|---|
| `example.com` | `mysite.org` | The site's domain |
| `u123456789` | `u123456789` | hPanel → Advanced → SSH Access (username) |
| `SERVER_IP` | `203.0.113.10` | hPanel → Advanced → SSH Access |
| `SSH_PORT` | `2222` | hPanel → Advanced → SSH Access |
| `GITHUB_USER` | `your-github-user` | The GitHub account that owns the repo |

The SSH port is shown in hPanel → Advanced → SSH Access (it is not 22).

**Layout on the server** - the app lives *next to* `public_html`, and
`public_html` is only a link to the app's `public/` folder, so `.env`,
`vendor/`, `storage/` and the source can never be reached from the web:

```
~/domains/example.com/
├── afraacms/                 the whole project (git clone)
│   ├── .env
│   └── public/               the only folder the web can see
└── public_html -> /home/u123456789/domains/example.com/afraacms/public
```

> **Do not use hPanel → Git (auto-deployment).** It can only deploy *into*
> `public_html` - which exposes the whole project and puts `/public` in every
> URL - and it never runs Composer, migrations or the cache rebuild.

---

## 1. Ship an update (the everyday routine)

1. **Merge into `main`.** Push to `develop`, open a pull request
   `develop → main` on GitHub and merge it. The live site only pulls `main`.
2. **Frontend changed?** If anything under `resources/` (CSS, JS, Blade views
   using new Tailwind classes), `vite.config.js` or `package.json` changed,
   build and upload on your computer:

   ```powershell
   cd C:\path\to\afraacms
   git checkout main
   git pull
   npm ci
   npm run build
   scp -P SSH_PORT -r public/build u123456789@SERVER_IP:domains/example.com/afraacms/public/
   git checkout develop
   ```

   PHP, route, config and migration changes need no rebuild.
3. **Deploy on the server:**

   ```bash
   ssh -p SSH_PORT u123456789@SERVER_IP
   cd ~/domains/example.com/afraacms
   bash deploy.sh
   ```

   It ends with `Deployed <commit>.`
4. **Check** the site with a hard refresh (Ctrl+F5).

`deploy.sh` puts the site in maintenance mode, pulls the code, installs
Composer packages, makes `public/build` readable, runs migrations, clears
every cache (including the CMS frontend caches) and rebuilds them, then brings
the site back up - even if a step fails. It stops early if `.env` or
`public/build` is missing.

---

## 2. One-time hPanel setup (new site)

1. **PHP version** - Advanced → PHP Configuration → choose **PHP 8.3** (or newer).
   On the PHP extensions tab make sure these are on: `pdo_mysql`, `mbstring`,
   `fileinfo`, `gd`, `zip`, `intl`, `bcmath`.
2. **Database** - Databases → MySQL Databases → create a database and user.
   Note the full names (Hostinger prefixes them, e.g. `u123456789_afraacms`)
   and the password.
3. **Mailbox** - Emails → create `no-reply@example.com`. The site sends
   contact, donation and visitor-book emails through it.
4. **SSL** - Security → SSL → install the free certificate, then turn on
   **Force HTTPS**.
5. **SSH** - Advanced → SSH Access → enable it.
6. **Git** - leave Advanced → Git **unconnected** (see the note at the top).

## 3. First install (new site)

Connect from your computer and check the versions:

```bash
ssh -p SSH_PORT u123456789@SERVER_IP
php -v        # must show 8.3+; if not, see Troubleshooting
composer --version
```

### 3.1 Give the server read access to the GitHub repo

The repository is private, so the server needs a deploy key. One Hostinger
account often hosts several sites, so give this repo **its own key file and
SSH alias** - never overwrite an existing `~/.ssh/id_rsa` / `id_ed25519`,
another site may depend on it.

```bash
ls ~/.ssh                                   # see what already exists
ssh-keygen -t ed25519 -C "example-afraacms" -f ~/.ssh/afraacms_deploy -N ""
cat ~/.ssh/afraacms_deploy.pub
```

On GitHub: **repo → Settings → Deploy keys → Add deploy key** (not your
account's SSH keys page), paste the key, leave "Allow write access" **off**.

Then add the alias - paste the whole block at once:

```bash
cat >> ~/.ssh/config <<'EOF'

Host github-afraacms
    HostName github.com
    User git
    IdentityFile ~/.ssh/afraacms_deploy
    IdentitiesOnly yes
EOF
chmod 600 ~/.ssh/config
ssh -T github-afraacms     # answer "yes"; expect "Hi GITHUB_USER/afraacms! You've successfully authenticated"
```

### 3.2 Get the code

```bash
cd ~/domains/example.com
git clone -b main git@github-afraacms:GITHUB_USER/afraacms.git afraacms
cd afraacms
composer install --no-dev --optimize-autoloader --no-interaction
```

### 3.3 Configure

```bash
cp .env.production.example .env
nano .env                 # fill every <...> value: domain, database, mailbox, Super Admin
php artisan key:generate
```

Must be set: `APP_ENV=production`, `APP_DEBUG=false`,
`APP_URL=https://example.com` (no `/public`).

### 3.4 Upload the frontend build

Shared hosting has no Node, so build on your computer and upload it - the
commands in section 1, step 2.

### 3.5 Database, storage, caches

Back on the server, in `~/domains/example.com/afraacms`:

```bash
php artisan migrate --force
php artisan db:seed --force      # first install only - see below
php artisan storage:link
php artisan optimize
chmod -R 775 storage bootstrap/cache
chmod 600 .env
```

In production, `db:seed` creates only what a real site needs: roles and
permissions, the Super Admin from `SUPER_ADMIN_*` in `.env`, default settings,
the starter menu and pages. Demo news and the `test@example.com` login are
skipped. It refuses to run if `SUPER_ADMIN_PASSWORD` is blank or `password`.
**Run it once only** - never on later deploys.

To load content instead of the blank starter pages, see section 4.

### 3.6 Point the domain at the app

Hostinger **recreates an empty `public_html` almost immediately** after you
move it away, so remove it and create the link in a single command, with an
absolute path:

```bash
cd ~/domains/example.com
mv public_html public_html.old     # keep it until the site works
rm -rf public_html                 # the empty one Hostinger may have recreated
ln -s /home/u123456789/domains/example.com/afraacms/public public_html
ls -la                             # must show: public_html -> /home/.../afraacms/public  (line starts with "l")
```

If `public_html` shows as a folder (`drwx...`) instead of a link, Hostinger
recreated it between commands - run the `rm -rf` and `ln -s` lines again.

### 3.7 Check and tidy up

| URL | Expected |
|---|---|
| `https://example.com` | The site, no `/public` in the URL |
| `https://example.com/login` | Login works with the `SUPER_ADMIN_*` details |
| `https://example.com/composer.json` | 404 |
| `https://example.com/.env` | 404 / 403 |

Then:

1. Delete the three `SUPER_ADMIN_*` lines from `.env` and run `php artisan optimize`.
2. In the admin, Settings → System: turn **registration_enabled** off.
3. Once everything works: `rm -rf ~/domains/example.com/public_html.old`.

---

## 4. Loading content

Pick one:

**A. Demo content from the repo** - a seeder (here `DemoContentSeeder`) builds the demo
pages, menus, projects, news, team and settings. Its images live in
`demo_frontend/images`, which is not in Git, so upload them first:

```bash
# server
mkdir -p ~/domains/example.com/afraacms/demo_frontend
# your computer
scp -P SSH_PORT -r demo_frontend/images u123456789@SERVER_IP:domains/example.com/afraacms/demo_frontend/
# server
cd ~/domains/example.com/afraacms
php artisan db:seed --class=DemoContentSeeder --force
php artisan optimize:clear && php artisan cms:clear-cache && php artisan optimize
rm -rf demo_frontend               # the images are now in the media library
```

**B. Copy content from another AfraaCMS site** (e.g. a demo already edited in
its admin). Copy only the content tables plus the media files - never `users`,
`roles`, `permissions`, `model_has_*`, `role_has_permissions`, `migrations`,
`sessions`, `cache*`, `jobs*`, so the target keeps its own logins.

1. Both sites must be on the same code: `php artisan migrate:status | grep -c Ran`
   must print the same number on both.
2. Back up the target database (phpMyAdmin → Export, or `mysqldump`).
3. Export from the source (phpMyAdmin → Export → Custom, **data only**) these
   tables: `settings`, `seo_meta`, `pages`, `sections`, `section_items`,
   `menus`, `menu_items`, `banners`, `media`, `media_items`, `galleries`,
   `gallery_items`, `gallery_section`, `projects`, `project_categories`,
   `section_project_category`, `section_project_item`, `stories`,
   `story_categories`, `section_story_category`, `section_story_item`,
   `news_posts`, `news_categories`, `section_news_category`,
   `section_news_post`, `team_members`, `team_categories`,
   `section_team_member`, `section_team_category`, `annual_reports`,
   `section_annual_report_item`, `featured_visitors`.
4. Empty the same tables on the target - phpMyAdmin → SQL:
   `SET FOREIGN_KEY_CHECKS = 0;` then one `DELETE FROM <table>;` per table
   above, then `SET FOREIGN_KEY_CHECKS = 1;`. (phpMyAdmin's "Empty" button
   silently skips tables other tables point to, which then fails the import
   with `#1062 Duplicate entry`.)
5. Import the export on the target with **"Enable foreign key checks" unticked**.
6. On the target, SQL: `UPDATE media_items SET uploaded_by = NULL;` (the
   source's user ids don't exist here).
7. Copy the files - the `media` table and this folder belong together:

   ```bash
   cp -r /path/to/source/storage/app/public/. ~/domains/example.com/afraacms/storage/app/public/
   cd ~/domains/example.com/afraacms
   php artisan optimize:clear && php artisan cms:clear-cache && php artisan optimize
   chmod -R 775 storage
   ```

---

## 5. Roles

| Role | Can do |
|---|---|
| **Super Admin** | Everything. Only one who can edit the developer credit, edit/delete a Super Admin account, or grant the Super Admin role. The last active Super Admin can't be removed. |
| **Admin** | Everything else, including users and roles - but can't touch Super Admins or grant permissions it doesn't hold. |
| **Editor** | Content only - no users, roles, permissions or activity log. |
| **Viewer** | Read-only content. |

Keep the developer account as Super Admin; give the client Admin, and their
staff Editor.

## 6. Things that are deliberately not set up

- **Queue worker** - nothing is queued: image conversions and emails run
  during the request. `.env` uses `QUEUE_CONNECTION=sync`, so anything queued
  in future runs immediately rather than waiting for a worker.
- **Cron** - there are no scheduled tasks yet. When one is added, create one
  hPanel cron job (Advanced → Cron Jobs → every minute):
  `/usr/bin/php /home/u123456789/domains/example.com/afraacms/artisan schedule:run`
- **Backups** - covered by the Hostinger plan's own backups (hPanel → Files →
  Backups), which include the database and every file, uploaded media too.
  To restore: pick a date there and restore the **database** and/or the
  **files** of `domains/example.com/afraacms`. After a restore run
  `php artisan optimize:clear && php artisan cms:clear-cache`.
  Before risky database work, also take a manual dump into `~/backups`
  (outside every `public_html`):
  `mysqldump --no-tablespaces -u DB_USER -p DB_NAME > ~/backups/before-change.sql`

## 7. Logs

Errors are written to `storage/logs/laravel-YYYY-MM-DD.log`, one file per day,
kept for 14 days:

```bash
tail -n 100 ~/domains/example.com/afraacms/storage/logs/laravel-$(date +%F).log
```

Never set `APP_DEBUG=true` on the live site - it shows secrets on error pages.

## 8. Troubleshooting

| Symptom | Likely cause and fix |
|---|---|
| `php -v` shows an old version | The CLI may not follow hPanel. Use the full path, e.g. `/opt/alt/php83/usr/bin/php`, and run updates as `PHP=/opt/alt/php83/usr/bin/php bash deploy.sh`. |
| **403 Forbidden** on the homepage | `public_html` is a folder, not a link - redo section 3.6. |
| "500 Server Error" page | Read today's log (section 7). Most often a wrong `.env` value - after editing `.env` run `php artisan optimize` again, because config is cached. |
| Page has no styling | `public/build` missing, old, or unreadable - redo section 1 step 2, then `bash deploy.sh` (it fixes the permissions). |
| Uploaded images are broken | The storage link is missing: `php artisan storage:link`. |
| Changes in the admin don't show on the site | `php artisan cms:clear-cache`. |
| `deploy.sh`: "Your local changes would be overwritten" | A tracked file was edited on the server (often `public/.htaccess` by an hPanel feature). `git status` / `git diff` to see it, copy any needed rule into the repo, then `git checkout -- <file>`. |
| Site stuck on the "back soon" page | `php artisan up` |
| Emails never arrive | Check the `MAIL_*` values match the mailbox; look for mail errors in the log. |
| "Permission denied" writing logs/cache | `chmod -R 775 storage bootstrap/cache` |
| Links or images use `http://` | `APP_ENV` must be `production` (the app then forces HTTPS links). |
| `ssh -T github-afraacms`: "Could not resolve hostname" | The `~/.ssh/config` block is missing or was pasted line by line - paste it again as one block (section 3.1). |
