FROM php:8.3-fpm

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libzip-dev \
    zip unzip \
    git curl \
    libpq-dev \
    libmagickwand-dev \
    imagemagick \
    ghostscript \
    && docker-php-ext-configure gd --with-jpeg --with-freetype \
    && docker-php-ext-install pdo pdo_pgsql pdo_mysql zip gd pcntl bcmath \
    && pecl install -o -f redis imagick \
    && docker-php-ext-enable redis imagick bcmath \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/pear ~/.pearrc

# Copy custom PHP configuration
COPY .docker/php/limit.ini /usr/local/etc/php/conf.d/limit.ini

# Copy ImageMagick security policy override (for Laravel image manipulation)
COPY .docker/imagemagick/policy.xml /etc/ImageMagick-6/policy.xml
COPY .docker/imagemagick/policy.xml /etc/ImageMagick-7/policy.xml

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Add user (non-root)
RUN groupadd --gid 1000 appuser \
    && useradd --uid 1000 -g appuser -G www-data --shell /bin/bash --create-home appuser

USER appuser
