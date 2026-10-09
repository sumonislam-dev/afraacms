# Site Deployment Runbook (example)

Every step used to put **example.com** live on Hostinger, in order, plus the
routine for future updates. The generic guide for any site is
[deployment.md](deployment.md).

> No passwords are stored here. Database and SSH passwords live only in
> hPanel and in the server's `.env`.

## Server facts

| Item | Value |
|---|---|
| SSH | `ssh -p SSH_PORT u123456789@SERVER_IP` |
| App folder | `~/domains/example.com/afraacms` |
| Web root | `~/domains/example.com/public_html` → link to `afraacms/public` |
| Branch deployed | `main` |
| GitHub SSH alias | `github-afraacms` (key `~/.ssh/afraacms_deploy`, read-only deploy key) |
| example.com database | `u123456789_site_db` / user `u123456789_site_user` |
| Demo site (content source) | `~/domains/example.org/public_html/demo` |
| Demo database | `u123456789_demo_db` / user `u123456789_demo_user` |
| Backups folder | `~/backups` (outside every `public_html`) |
| PHP (CLI) | 8.3+ |

---

# Part 1 - Ship an update (every time)

### Step 1 - Merge into `main`
Push your work to `develop`, then on GitHub open a pull request
**develop → main** and merge it. The live site only pulls `main`.

### Step 2 - Frontend build (only if CSS / JS / Blade views changed)
On your PC, PowerShell, one line at a time:

```powershell
cd C:\path\to\afraacms
git checkout main
git pull
npm ci
npm run build
scp -P SSH_PORT -r public/build u123456789@SERVER_IP:domains/example.com/afraacms/public/
git checkout develop
```

Skip this step for PHP / route / config / migration-only changes.

### Step 3 - Deploy on the server

```bash
ssh -p SSH_PORT u123456789@SERVER_IP
cd ~/domains/example.com/afraacms
bash deploy.sh
```

Ends with `Deployed <commit>.` - `deploy.sh` turns on maintenance mode, pulls,
runs Composer, fixes `public/build` permissions, migrates, rebuilds caches and
turns the site back on.

> Until the updated `deploy.sh` (with the permission fix) is merged into
> `main`, also run after an upload:
> ```bash
> find public/build -type d -exec chmod 755 {} \;
> find public/build -type f -exec chmod 644 {} \;
> ```

### Step 4 - Check
Open `https://example.com` and hard-refresh (**Ctrl+F5**).

---

# Part 2 - First-time setup (what was done, in order)

### 2.1 Stop hPanel Git
1. hPanel → Websites → example.com → Advanced → **GIT** → turn **Auto-deployment** off.
2. **⋮ → Disconnect from repository.**
3. GitHub → Settings → Applications → **Authorized GitHub Apps** → Hostinger → **Revoke**.
4. Check the repo has no leftover hooks/keys:
   repo → Settings → **Webhooks** (empty) and **Deploy keys** (empty).

Never click "Connect with GitHub" in hPanel again.

### 2.2 Back up the old site
1. File Manager → `public_html/.env` → Download (turn on *Show hidden files*).
2. phpMyAdmin → database → **Export** → Go.
3. Download `public_html/storage/app/public` if it has files.

### 2.3 Enable SSH and connect
hPanel → Advanced → **SSH Access** → enable. From the PC:

```bash
ssh -p SSH_PORT u123456789@SERVER_IP
php -v                 # 8.3+
composer --version
```

### 2.4 Deploy key (its own key - other sites use `~/.ssh/id_rsa`)

```bash
ssh-keygen -t ed25519 -C "site-afraacms" -f ~/.ssh/afraacms_deploy -N ""
cat ~/.ssh/afraacms_deploy.pub
```

GitHub → **afraacms → Settings → Deploy keys → Add deploy key**, title
`site-hostinger`, **write access off**. (Not the account's *SSH and GPG keys*
page - never delete keys there, other servers use them.)

Paste this whole block at once:

```bash
cat >> ~/.ssh/config <<'EOF'

Host github-afraacms
    HostName github.com
    User git
    IdentityFile ~/.ssh/afraacms_deploy
    IdentitiesOnly yes
EOF
chmod 600 ~/.ssh/config
ssh -T github-afraacms
```

Answer `yes`. Expected: `Hi GITHUB_USER/afraacms! You've successfully authenticated`.

### 2.5 Clone outside `public_html`

```bash
cd ~/domains/example.com
git clone -b main git@github-afraacms:GITHUB_USER/afraacms.git afraacms
cd afraacms
composer install --no-dev --optimize-autoloader --no-interaction
```

### 2.6 `.env`

```bash
cp ../public_html/.env .env
nano .env
```

Set: `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://example.com`.
Save: **Ctrl+O**, **Enter**, **Ctrl+X**.

```bash
php artisan migrate:status      # all "Ran" = tables exist
php artisan tinker --execute="echo App\Models\User::count();"
```

### 2.7 Create the Super Admin (only because the user count was 0)
Add to the bottom of `.env`:

```
SUPER_ADMIN_NAME="Your Name"
SUPER_ADMIN_EMAIL=<your email>
SUPER_ADMIN_PASSWORD=<long unique password>
```

```bash
php artisan config:clear
php artisan db:seed --force
php artisan tinker --execute="echo App\Models\User::count();"    # 1
```

### 2.8 Upload the frontend build
Part 1, Step 2. Then on the server:

```bash
ls ~/domains/example.com/afraacms/public/build/manifest.json
```

### 2.9 Storage, caches, permissions

```bash
cd ~/domains/example.com/afraacms
cp -r ../public_html/storage/app/public/. storage/app/public/ 2>/dev/null
php artisan storage:link
find public/build -type d -exec chmod 755 {} \;
find public/build -type f -exec chmod 644 {} \;
php artisan optimize
chmod -R 775 storage bootstrap/cache
chmod 600 .env
```

### 2.10 Switch the domain to the app
Hostinger recreates an empty `public_html` right after it is moved, so:

```bash
cd ~/domains/example.com
mv public_html public_html.old
rm -rf public_html
ln -s /home/u123456789/domains/example.com/afraacms/public public_html
ls -la
```

Must show (line starts with **`l`**):

```
lrwxrwxrwx ... public_html -> /home/u123456789/domains/example.com/afraacms/public
```

If it shows `drwxr-xr-x public_html` instead, run the `rm -rf` and `ln -s`
lines again.

### 2.11 Check

| URL | Expected |
|---|---|
| `https://example.com` | Site loads, no `/public` |
| `https://example.com/login` | Login works |
| `https://example.com/composer.json` | 404 |
| `https://example.com/.env` | 404 / 403 |

---

# Part 3 - Copy content from demo.example.org

Copies only content; `users`, `roles`, `permissions`, `migrations` stay
untouched, so your own login is kept.

### 3.1 Same code version on both?

```bash
cd ~/domains/example.org/public_html/demo && php artisan migrate:status | grep -c Ran
cd ~/domains/example.com/afraacms && php artisan migrate:status | grep -c Ran
```

Both numbers must match.

### 3.2 Back up example.com (example.com DB password)

```bash
mkdir -p ~/backups
mysqldump --no-tablespaces -u u123456789_site_user -p u123456789_site_db > ~/backups/site-before-copy.sql
```

### 3.3 Export from the demo
phpMyAdmin → `u123456789_demo_db` → **Export → Custom** → select only:

`annual_reports`, `banners`, `featured_visitors`, `galleries`,
`gallery_items`, `gallery_section`, `media`, `media_items`, `menu_items`,
`menus`, `news_categories`, `news_posts`, `pages`, `project_categories`,
`projects`, `section_annual_report_item`, `section_items`,
`section_news_category`, `section_news_post`, `section_project_category`,
`section_project_item`, `section_story_category`, `section_story_item`,
`section_team_category`, `section_team_member`, `sections`, `seo_meta`,
`settings`, `stories`, `story_categories`, `team_categories`, `team_members`

Format **SQL**, **data only**.

### 3.4 Empty those tables on example.com
phpMyAdmin → `u123456789_site_db` → **SQL** → Go:

```sql
SET FOREIGN_KEY_CHECKS = 0;
DELETE FROM menu_items;
DELETE FROM menus;
DELETE FROM annual_reports;
DELETE FROM banners;
DELETE FROM featured_visitors;
DELETE FROM gallery_section;
DELETE FROM gallery_items;
DELETE FROM galleries;
DELETE FROM media;
DELETE FROM media_items;
DELETE FROM news_posts;
DELETE FROM news_categories;
DELETE FROM section_annual_report_item;
DELETE FROM section_items;
DELETE FROM section_news_category;
DELETE FROM section_news_post;
DELETE FROM section_project_category;
DELETE FROM section_project_item;
DELETE FROM section_story_category;
DELETE FROM section_story_item;
DELETE FROM section_team_category;
DELETE FROM section_team_member;
DELETE FROM sections;
DELETE FROM pages;
DELETE FROM projects;
DELETE FROM project_categories;
DELETE FROM stories;
DELETE FROM story_categories;
DELETE FROM team_members;
DELETE FROM team_categories;
DELETE FROM seo_meta;
DELETE FROM settings;
SET FOREIGN_KEY_CHECKS = 1;
```

(phpMyAdmin's *Empty* button skips tables others point to - e.g. `menus` -
which makes the import fail with `#1062 Duplicate entry '1'`.)

### 3.5 Import
phpMyAdmin → example.com database → **Import** → the demo file →
**untick "Enable foreign key checks"** → Import. Must finish with no red error.

### 3.6 Fix uploader ids (SQL tab)

```sql
UPDATE media_items SET uploaded_by = NULL;
```

### 3.7 Copy images and PDFs, clear caches

```bash
cp -r ~/domains/example.org/public_html/demo/storage/app/public/. ~/domains/example.com/afraacms/storage/app/public/
cd ~/domains/example.com/afraacms
php artisan optimize:clear
php artisan cms:clear-cache
php artisan optimize
chmod -R 775 storage
```

### 3.8 Undo (if needed)

```bash
mysql -u u123456789_site_user -p u123456789_site_db < ~/backups/site-before-copy.sql
```

---

# Part 4 - Finish and tidy up

- [ ] Delete the three `SUPER_ADMIN_*` lines from `.env`, then `php artisan optimize`.
- [ ] `rm -rf ~/domains/example.com/public_html.old` (only once everything works).
- [ ] Download `~/backups/*.sql` to the PC, then delete them from the server.
- [ ] Mail: hPanel → Emails → create `no-reply@example.com`; fill `MAIL_USERNAME`,
      `MAIL_PASSWORD`, `MAIL_FROM_ADDRESS` in `.env`; `php artisan optimize`.
- [ ] Admin → Settings → System → **registration_enabled** off.
- [ ] Admin → Users: deactivate any account that isn't needed.
- [ ] Demo leftovers: Media "task1", a team member's service period "e.g. 2022",
      empty "Notice" news post, expired "Admission Open" banners ,
      trashed "Reports" page.
- [ ] Give client staff accounts: manager = **Admin**, staff = **Editor**; keep
      your own account **Super Admin**.
- [ ] Later: move `example.org` and `demo.example.org` out of `public_html` the
      same way (Part 2) - they are still exposed.

---

# Part 5 - Rollback and troubleshooting

**Put the old site back** (undo Part 2.10):

```bash
cd ~/domains/example.com && rm public_html && mv public_html.old public_html
```

**Read errors:**

```bash
tail -n 50 ~/domains/example.com/afraacms/storage/logs/laravel-$(date +%F).log
```

| Symptom | Fix |
|---|---|
| 403 Forbidden on homepage | `public_html` is a folder, not a link - redo 2.10 |
| 500 error | Read the log above; after any `.env` edit run `php artisan optimize` |
| No styling | `public/build` missing or unreadable - Part 1 Step 2, then the two `find … chmod` lines |
| Images broken | `php artisan storage:link` |
| Admin change not visible | `php artisan cms:clear-cache` |
| Stuck on "back soon" | `php artisan up` |
| `deploy.sh`: local changes would be overwritten | `git status` on the server and send the output |
| `#1062 Duplicate entry` on import | A table wasn't emptied - rerun 3.4 |
| `ssh -T github-afraacms`: could not resolve hostname | Paste the `~/.ssh/config` block again as one block |
