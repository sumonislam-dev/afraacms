# Deploying AfraaCMS to Hostinger

How to put AfraaCMS on Hostinger shared hosting (hPanel, with SSH) and how
to ship updates afterwards. Replace these placeholders throughout:

| Placeholder | Example | Where to find it |
|---|---|---|
| `example.com` | `afraaworld.com` | The site's domain |
| `u123456789` | `u123456789` | hPanel → Advanced → SSH Access (username) |
| `SERVER_IP` | `153.92.0.10` | hPanel → Advanced → SSH Access |

Hostinger's SSH port is **65002**, not 22.

---

## 1. One-time hPanel setup

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

## 2. First install

Connect from your computer:

```bash
ssh -p 65002 u123456789@SERVER_IP
php -v        # must show 8.3+; if not, see Troubleshooting
composer -V   # Composer 2 is preinstalled
```

### 2.1 Give the server read access to the GitHub repo

The repository is private, so the server needs its own deploy key:

```bash
ssh-keygen -t ed25519 -C "hostinger-deploy" -f ~/.ssh/id_ed25519 -N ""
cat ~/.ssh/id_ed25519.pub
```

On GitHub: repo → Settings → Deploy keys → **Add deploy key**, paste the key,
leave "Allow write access" **off**. Then check it works:

```bash
ssh -T git@github.com   # "Hi sumonislam-dev/afraacms! You've successfully authenticated"
```

### 2.2 Get the code

The app lives **next to** `public_html`, never inside it, so `.env`, `storage/`
and the source are not reachable from the web:

```bash
cd ~/domains/example.com
git clone -b main git@github.com:sumonislam-dev/afraacms.git afraacms
cd afraacms
composer install --no-dev --optimize-autoloader --no-interaction
```

### 2.3 Configure

```bash
cp .env.production.example .env
nano .env                 # fill every <...> value: domain, database, mailbox, Super Admin
php artisan key:generate
```

### 2.4 Upload the frontend build

Shared hosting usually has no Node, so build on your computer and upload the
result (run these **locally**, from the project folder):

```bash
npm ci && npm run build
scp -P 65002 -r public/build u123456789@SERVER_IP:~/domains/example.com/afraacms/public/
```

### 2.5 Database, storage, caches

Back on the server, in `~/domains/example.com/afraacms`:

```bash
php artisan migrate --force
php artisan db:seed --force      # first install only - see below
php artisan storage:link
php artisan optimize
chmod -R 775 storage bootstrap/cache
```

In production, `db:seed` creates only what a real site needs: roles and
permissions, the Super Admin from `SUPER_ADMIN_*` in `.env`, default settings,
the starter menu and pages. Demo news and the `test@example.com` login are
skipped. It refuses to run if `SUPER_ADMIN_PASSWORD` is blank or `password`.
**Run it once only** - never on later deploys.

### 2.6 Point the domain at the app

Replace `public_html` with a link to the app's `public` folder:

```bash
cd ~/domains/example.com
mv public_html public_html.old     # keep it until the site works, then delete it
ln -s afraacms/public public_html
```

Open `https://example.com` - the homepage should load. Log in at
`https://example.com/login` with the `SUPER_ADMIN_*` details, then delete
those three lines from `.env` and run `php artisan optimize`.

## 3. Shipping an update

1. Merge the changes into `main` and push.
2. If any CSS/JS or Blade view changed, rebuild and upload the frontend
   (step 2.4) - it is safest to do this on every deploy.
3. On the server:

```bash
cd ~/domains/example.com/afraacms
bash deploy.sh
```

`deploy.sh` puts the site in maintenance mode, pulls the code, installs
Composer packages, runs migrations, clears every cache (including the CMS
frontend caches) and rebuilds them, then brings the site back up - even if a
step fails. It stops early if `.env` or `public/build` is missing.

## 4. Things that are deliberately not set up

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

## 5. Logs

Errors are written to `storage/logs/laravel-YYYY-MM-DD.log`, one file per day,
kept for 14 days:

```bash
tail -n 100 ~/domains/example.com/afraacms/storage/logs/laravel-$(date +%F).log
```

Never set `APP_DEBUG=true` on the live site - it shows secrets on error pages.

## 6. Troubleshooting

| Symptom | Likely cause and fix |
|---|---|
| `php -v` shows an old version | The CLI may not follow hPanel. Use the full path, e.g. `/opt/alt/php83/usr/bin/php`, and run updates as `PHP=/opt/alt/php83/usr/bin/php bash deploy.sh`. |
| "500 Server Error" page | Read today's log (section 5). Most often a wrong `.env` value - after editing `.env` run `php artisan optimize` again, because config is cached. |
| Page has no styling | `public/build` was not uploaded or is from an old build - redo step 2.4. |
| Uploaded images are broken | The storage link is missing: `php artisan storage:link`. |
| Changes in the admin don't show on the site | `php artisan cms:clear-cache`. |
| Emails never arrive | Check the `MAIL_*` values match the mailbox; look for mail errors in the log. |
| "Permission denied" writing logs/cache | `chmod -R 775 storage bootstrap/cache` |
| Links or images use `http://` | `APP_ENV` must be `production` (the app then forces HTTPS links). |
