# Use the official PHP image as the base image
FROM php:8.2-fpm

# Set working directory inside the container
WORKDIR /var/www

# Install system dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    libonig-dev \
    libzip-dev \
    libmcrypt-dev \
    libicu-dev \
    libxml2-dev \
    default-mysql-client \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql mbstring zip exif pcntl intl xml sockets

# Install Composer globally
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy PHP configuration file
COPY ./docker/php/local.ini /usr/local/etc/php/conf.d/

# Expose port 9000
EXPOSE 9000

# Start PHP-FPM server
CMD ["php-fpm"]
