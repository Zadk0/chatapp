#!/bin/sh
set -e

cd /var/www/html

# ─── GENERAR APP KEY SI NO EXISTE ─────────────────────────────────────────
if [ -z "$APP_KEY" ]; then
    echo "Generando APP_KEY..."
    php artisan key:generate --force
fi

# ─── CREAR .env DESDE VARIABLES DE ENTORNO ───────────────────────────────
cat > .env << EOF
APP_NAME="${APP_NAME:-ChatApp}"
APP_ENV="${APP_ENV:-production}"
APP_KEY="${APP_KEY}"
APP_DEBUG="${APP_DEBUG:-false}"
APP_URL="${APP_URL:-http://localhost}"

DB_CONNECTION="${DB_CONNECTION:-pgsql}"
DB_HOST="${DB_HOST}"
DB_PORT="${DB_PORT:-5432}"
DB_DATABASE="${DB_DATABASE}"
DB_USERNAME="${DB_USERNAME}"
DB_PASSWORD="${DB_PASSWORD}"

BROADCAST_DRIVER="${BROADCAST_DRIVER:-reverb}"
CACHE_DRIVER="${CACHE_DRIVER:-file}"
SESSION_DRIVER="${SESSION_DRIVER:-database}"
QUEUE_CONNECTION="${QUEUE_CONNECTION:-database}"

REVERB_APP_ID="${REVERB_APP_ID:-chatapp}"
REVERB_APP_KEY="${REVERB_APP_KEY}"
REVERB_APP_SECRET="${REVERB_APP_SECRET}"
REVERB_HOST="${REVERB_HOST:-0.0.0.0}"
REVERB_PORT="${REVERB_PORT:-8080}"
REVERB_SCHEME="${REVERB_SCHEME:-https}"

MAIL_MAILER="${MAIL_MAILER:-smtp}"
MAIL_HOST="${MAIL_HOST:-smtp.gmail.com}"
MAIL_PORT="${MAIL_PORT:-587}"
MAIL_USERNAME="${MAIL_USERNAME}"
MAIL_PASSWORD="${MAIL_PASSWORD}"
MAIL_ENCRYPTION="${MAIL_ENCRYPTION:-tls}"
MAIL_FROM_ADDRESS="${MAIL_FROM_ADDRESS:-noreply@chatapp.com}"
MAIL_FROM_NAME="${MAIL_FROM_NAME:-ChatApp}"
EOF

# ─── CREAR DIRECTORIOS NECESARIOS ────────────────────────────────────────
mkdir -p storage/framework/{cache,sessions,views,testing}
mkdir -p storage/logs
mkdir -p bootstrap/cache

# ─── PERMISOS ─────────────────────────────────────────────────────────────
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# ─── LIMPIAR CACHÉ ────────────────────────────────────────────────────────
php artisan config:clear
php artisan route:clear
php artisan view:clear

# ─── OPTIMIZAR PARA PRODUCCIÓN ────────────────────────────────────────────
php artisan config:cache
php artisan route:cache
php artisan view:cache

# ─── EJECUTAR MIGRACIONES ─────────────────────────────────────────────────
echo "Ejecutando migraciones..."
php artisan migrate --force

# ─── CREAR ENLACE SIMBÓLICO STORAGE ─────────────────────────────────────
php artisan storage:link --force 2>/dev/null || true

# ─── CREAR TABLAS DE COLA ─────────────────────────────────────────────────
php artisan queue:table 2>/dev/null || true
php artisan migrate --force

echo "✅ ChatApp lista! Iniciando servicios..."

# ─── INICIAR SUPERVISORD ─────────────────────────────────────────────────
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
