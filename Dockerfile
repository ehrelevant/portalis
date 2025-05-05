# Build node frontend
FROM node:lts AS builder

WORKDIR /var/www

# Copy lockfiles
COPY pnpm-lock.yaml package.json /var/www/

RUN npm i -g pnpm
RUN pnpm install

RUN pnpm build
RUN pnpm prune --prod

# PHP Deployment Setup
FROM php:8.3-fpm

# Copy lockfiles
COPY composer.lock composer.json /var/www/

# Copy Node build files
COPY --from=builder ./public/build ./public/build

# Set working directory
WORKDIR /var/www

# Install dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libonig-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    libzip-dev

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install extensions
RUN docker-php-ext-install pdo_mysql mbstring zip exif pcntl

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Add user for laravel application
RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www

# Copy application directory contents
COPY . /var/www

# Copy application directory permissions
COPY --chown=www:www . /var/www

# Change current user to www
USER www

# Expose port 9000
EXPOSE 9000

# Start php-fpm server
CMD ["php-fpm"]