#!/bin/sh
set -e

cd /var/www/html

# A generated key must survive restarts: it is written into the storage volume
# and reused, otherwise every restart would invalidate sessions and encrypted
# values. An APP_KEY passed in through the environment always wins.
KEY_FILE=storage/app/.app-key
if [ -z "${APP_KEY}" ] && [ -f "$KEY_FILE" ]; then
  APP_KEY="$(cat "$KEY_FILE")"
  export APP_KEY
fi
if [ -z "${APP_KEY}" ]; then
  APP_KEY="$(php artisan key:generate --show)"
  export APP_KEY
  printf '%s\n' "$APP_KEY" > "$KEY_FILE"
  echo "entrypoint: generated APP_KEY and stored it in $KEY_FILE"
fi

echo "entrypoint: running migrations"
migrated=""
for i in $(seq 1 30); do
  if php artisan migrate --force; then
    migrated=1
    break
  fi
  echo "entrypoint: database not ready yet (attempt $i)"
  sleep 2
done
if [ -z "$migrated" ]; then
  echo "entrypoint: could not reach the database, giving up"
  exit 1
fi

# Demo numbers for the mosque's review; skipped as soon as the ledger has any
# real rows, so restoring a backup or entering real data is never overwritten.
if [ "${SEED_DEMO_DATA:-true}" = "true" ]; then
  ROWS="$(php -r '
    require "vendor/autoload.php";
    $app = require "bootstrap/app.php";
    $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
    echo App\Models\TransaksiKeuangan::count();
  ')"
  if [ "$ROWS" = "0" ]; then
    echo "entrypoint: ledger is empty, running the demo seeder"
    php artisan db:seed --force
  else
    echo "entrypoint: ledger already has $ROWS transactions, skipping the seeder"
  fi
fi

# public/storage is a symlink created at runtime: the storage directory itself
# lives on a volume, so the link cannot be baked into the image. --force makes
# a stale link from a previous image harmless.
echo "entrypoint: linking public storage"
php artisan storage:link --force

echo "entrypoint: caching configuration, routes and views"
php artisan optimize

echo "entrypoint: ready"
exec php-fpm
