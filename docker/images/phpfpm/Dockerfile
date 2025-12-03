# Base stage with shared configuration
FROM php:8.4-fpm-alpine AS base

RUN adduser -u 1000 -G www-data -D appuser && \
    chown -R appuser:www-data /var/www/html

RUN apk update && apk add --no-cache \
    autoconf \
    build-base \
    bzip2-dev \
    curl \
    curl-dev \
    git \
    freetype-dev \
    icu-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    libwebp-dev \
    libxslt-dev \
    libzip-dev \
    linux-headers \
    oniguruma-dev \
    postgresql-client \
    postgresql-dev \
    unzip \
    zip \
    && rm -rf /var/cache/apk/*

RUN pecl channel-update pecl.php.net \
    && pecl install redis-6.1.0 \
    && pecl clear-cache

RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install \
        bcmath \
        bz2 \
        exif \
        gd \
        intl \
        mbstring \
        opcache \
        pcntl \
        pdo \
        pdo_pgsql \
        xsl \
        zip \
    && docker-php-ext-enable \
        redis

COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Development stage (default target)
FROM base AS development

# MariaDB support - COMMENTED OUT after PostgreSQL migration
# RUN apk add --no-cache mysql-client mysql-dev \
#     && docker-php-ext-install mysqli pdo_mysql

# Install and enable xdebug for development
RUN pecl install xdebug-3.4.1 \
    && docker-php-ext-enable xdebug

USER appuser

# Production stage (PostgreSQL only, no MySQL)
FROM base AS production

RUN echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.validate_timestamps=0" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.max_accelerated_files=10000" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.memory_consumption=192" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.max_wasted_percentage=10" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.interned_strings_buffer=16" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.fast_shutdown=1" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini

USER appuser
