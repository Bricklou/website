# ==========
# PHP builder
# ==========
FROM php:8.2.0RC6-fpm-bullseye as php_base

ENV APP_DEBUG=false
ENV APP_ENV=production

RUN set -ex \
    && apt-get update \
    && apt-get install -y $PHPIZE_DEPS libxml2-dev libzip-dev libwebp-dev \
    libxml2 zip libpq-dev \
    pandoc texlive-base \
    # Install extensions for php
    && pecl install redis \
    && docker-php-ext-install -j$(nproc) bcmath pgsql pdo pdo_pgsql zip pcntl \
    && docker-php-ext-enable redis \
    # Install laravel dependencies
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    # Removing useless packages
    && rm -rf /var/lib/{apt,dpkg,cache,log}/

WORKDIR /www
COPY --chown=docker:docker . /www

# ==========
# NodeJS builder
# ==========
FROM node:bullseye as node_dependencies

ENV PUPPETEER_SKIP_CHROMIUM_DOWNLOAD=false

COPY --from=php_base /www /www
WORKDIR /www
RUN npm set progress=false && \
    npm config set depth 0 && \
    npm clean-install && \
    npm run production && \
    rm -rf node_modules

# ========
# PHP base
# ========
FROM php_base

COPY --from=node_dependencies --chown=docker:docker /www /www

WORKDIR /www

COPY ./docker/entrypoint.sh /entrypoint.sh

RUN composer install --no-dev --no-interaction --no-progress --optimize-autoloader \
    && chmod +x /entrypoint.sh

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
    && sed -i "s/upload_max_filesize = 2M/upload_max_filesize = 100M/" "$PHP_INI_DIR/php.ini" \
    && sed -i "s/post_max_size = 8M/post_max_size = 100M/" "$PHP_INI_DIR/php.ini"

WORKDIR /var/www/html

EXPOSE 9000
CMD ["/entrypoint.sh"]
