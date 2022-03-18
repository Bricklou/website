# ==========
# PHP builder
# ==========
FROM php:8-fpm-alpine as php_base

ENV APP_DEBUG=false
ENV APP_ENV=production

RUN set -ex \
    && apk add --no-cache --update --virtual .build-deps \
    libxml2-dev libzip-dev libwebp-dev oniguruma-dev git $PHPIZE_DEPS \
    && apk add --no-cache --update \
    libxml2 libzip oniguruma postgresql-dev \
    # Install extensions for php
    && pecl install redis \
    && docker-php-ext-install -j$(nproc) bcmath pgsql pdo pdo_pgsql zip pcntl \
    && docker-php-ext-enable redis \
    # Install laravel dependencies
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    # Removing useless packages
    && apk del --no-cache .build-deps \
    && rm -rf /var/cache/apk/*

WORKDIR /app
COPY --chown=docker:docker . /app

# ==========
# NodeJS builder
# ==========
FROM node:alpine as node_dependencies

ENV PUPPETEER_SKIP_CHROMIUM_DOWNLOAD=false

COPY --from=php_base /app /app
WORKDIR /app
RUN npm set progress=false && \
    npm config set depth 0 && \
    npm clean-install && \
    npm run production && \
    rm -rf node_modules

COPY /app /app

# ========
# PHP base
# ========
FROM php_base

COPY --from=node_dependencies --chown=docker:docker /app /app

WORKDIR /app

COPY ./docker/entrypoint.sh /entrypoint.sh

RUN composer install --no-dev --no-interaction --no-progress --optimize-autoloader \
    && chmod +x /entrypoint.sh

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
    && sed -i "s/upload_max_filesize = 2M/upload_max_filesize = 100M/" "$PHP_INI_DIR/php.ini" \
    && sed -i "s/post_max_size = 8M/post_max_size = 100M/" "$PHP_INI_DIR/php.ini"

WORKDIR /var/www/html

EXPOSE 9000
CMD ["/entrypoint.sh"]
