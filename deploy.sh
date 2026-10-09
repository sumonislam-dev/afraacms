#!/usr/bin/env bash
#
# Update a deployed AfraaCMS site to the latest code on its branch.
# Run on the server, from the app folder:   bash deploy.sh
#
# First-time setup is different - see docs/deployment.md.
# Override the binaries if the server's defaults are wrong, e.g.
#   PHP=/opt/alt/php83/usr/bin/php bash deploy.sh

set -euo pipefail
cd "$(dirname "$0")"

PHP="${PHP:-php}"
COMPOSER="${COMPOSER:-composer}"

if [ ! -f .env ]; then
    echo "No .env file. Copy .env.production.example to .env first (docs/deployment.md)." >&2
    exit 1
fi

# Visitors get a "back soon" page while files and the database change; the
# trap brings the site back up even if a step below fails.
"$PHP" artisan down --retry=60 || true
trap '"$PHP" artisan up' EXIT

git pull --ff-only

"$COMPOSER" install --no-dev --optimize-autoloader --no-interaction

# Frontend assets: built here if the server has Node, otherwise they must be
# built locally and uploaded to public/build before running this script.
if command -v npm >/dev/null 2>&1; then
    npm ci --no-audit --no-fund
    npm run build
fi
if [ ! -f public/build/manifest.json ]; then
    echo "public/build is missing. Build locally and upload it (docs/deployment.md)." >&2
    exit 1
fi
# An upload from Windows can arrive owner-only (700/600), which hides the
# CSS/JS from the web server - make it world-readable every time.
find public/build -type d -exec chmod 755 {} +
find public/build -type f -exec chmod 644 {} +

"$PHP" artisan migrate --force

# Makes uploaded media public; harmless if the link already exists.
[ -e public/storage ] || "$PHP" artisan storage:link

# Drop every cached config/route/view and the CMS frontend caches, then
# rebuild the framework caches against the new code.
"$PHP" artisan optimize:clear
"$PHP" artisan cms:clear-cache
"$PHP" artisan optimize

echo "Deployed $(git rev-parse --short HEAD)."
