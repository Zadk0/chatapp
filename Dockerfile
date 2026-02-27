# Dockerfile - Optimizado para Render.com
FROM php:8.2-fpm-alpine

# ─── DEPENDENCIAS DEL SISTEMA ─────────────────────────────────────────────
RUN apk add --no-cache \
    nginx \
    nodejs \
    npm \
    git \
    curl \
    zip \
    unzip \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    postgresql-dev \
    supervisor \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        gd \
        pdo \
        pdo_pgsql \
        pgsql \
        bcmath \
        opcache \
        pcntl \
        sockets

# ─── COMPOSER ─────────────────────────────────────────────────────────────
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# ─── DIRECTORIO DE TRABAJO ────────────────────────────────────────────────
WORKDIR /var/www/html

# ─── COPIAR PROYECTO ──────────────────────────────────────────────────────
COPY . .

# ─── INSTALAR DEPENDENCIAS PHP ────────────────────────────────────────────
RUN composer install --no-dev --optimize-autoloader --no-interaction

# ─── INSTALAR DEPENDENCIAS JS ─────────────────────────────────────────────
RUN npm ci && npm run build && rm -rf node_modules

# ─── PERMISOS ─────────────────────────────────────────────────────────────
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# ─── CONFIGURACIÓN NGINX ──────────────────────────────────────────────────
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/default.conf /etc/nginx/http.d/default.conf

# ─── SUPERVISOR ───────────────────────────────────────────────────────────
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# ─── SCRIPT DE INICIO ────────────────────────────────────────────────────
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 80

CMD ["/start.sh"]
